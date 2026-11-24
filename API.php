<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter;

use Piwik\Container\StaticContainer;
use Piwik\Option;
use Piwik\Piwik;
use Piwik\Plugin\API as PluginAPI;
use Piwik\Plugin\Manager as PluginManager;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\DateWindow;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\SiteDirectory;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\VisitFinder;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Feedback\PluginFeedback;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Input\BadRequestException;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Input\DateRange;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Input\HttpErrorStatus;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Input\InvalidInputException;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Input\RequestValue;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\DeletionPreview;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\Job;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\JobControl;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\JobFactory;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\JobNotFoundException;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\JobRepository;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\JobRunner;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\MatomoRunnerLock;
use Piwik\Translation\Translator;
use Piwik\Version;

/**
 * Delete visits by segment, date range and website(s). All methods require super user access.
 * Parameters are untyped on purpose: Matomo passes HTTP parameters as strings.
 */
class API extends PluginAPI
{
    /** Seconds one processJobs() call may work – the admin page calls it in a loop. */
    private const PROCESS_BUDGET_SECONDS = 15;

    /** Allowed values of getJobs()' filters (empty: no filter). */
    private const STATUSES = [Job::STATUS_QUEUED, Job::STATUS_RUNNING, Job::STATUS_HELD, Job::STATUS_DONE, Job::STATUS_CANCELLED, Job::STATUS_FAILED];
    private const SOURCES = [Job::SOURCE_UI, Job::SOURCE_API, Job::SOURCE_CONSOLE, Job::SOURCE_RULE];

    public function __construct(
        private JobFactory $factory,
        private JobControl $control,
        private JobRepository $jobs,
        private JobRunner $runner,
        private VisitFinder $finder,
        private DeletionPreview $preview,
        private SiteDirectory $sites
    ) {
    }

    /**
     * Counts the visits that match a segment in a date range, without deleting anything; use previewDeletion() for
     * the count per website and a sample.
     *
     * @param int|string $idSite One website ID, a comma-separated list of IDs or "all".
     * @param string $segment Matomo segment definition, for example "visitIp>=192.0.2.0;visitIp<=192.0.2.255"; required,
     *                        a segment without a valid condition is refused.
     * @param string $dateFrom First day, YYYY-MM-DD, counted in each website's time zone.
     * @param string $dateTo Last day, YYYY-MM-DD (inclusive), not in the future.
     * @return int Number of matching visits over all given websites.
     */
    public function countVisits($idSite, $segment, $dateFrom, $dateTo)
    {
        Piwik::checkUserHasSuperUserAccess();
        $input = $this->validate($idSite, $segment, $dateFrom, $dateTo);

        $total = 0;
        foreach ($input['idSites'] as $id) {
            $total += $this->finder->count($id, $input['segment'], $this->window($id, $input['range']));
        }

        return $total;
    }

    /**
     * Deprecated since 2.1.0: returns the IDs of matching visits; use previewDeletion() instead.
     *
     * @deprecated since 2.1.0, use previewDeletion()
     * @param int|string $idSite One website ID, a comma-separated list of IDs or "all".
     * @param string $segment Matomo segment definition (required).
     * @param string $dateFrom First day, YYYY-MM-DD.
     * @param string $dateTo Last day, YYYY-MM-DD (inclusive).
     * @param int|string $limit Maximum number of IDs, 1 to 100000 (default 100).
     * @return int[] Visit IDs (idvisit).
     */
    public function findVisits($idSite, $segment, $dateFrom, $dateTo, $limit = 100)
    {
        Piwik::checkUserHasSuperUserAccess();
        $input = $this->validate($idSite, $segment, $dateFrom, $dateTo);
        $limit = max(1, min((int) $limit, 100000));

        $ids = [];
        foreach ($input['idSites'] as $id) {
            $ids = array_merge($ids, $this->finder->findIds($id, $input['segment'], $this->window($id, $input['range']), $limit - count($ids)));
            if (count($ids) >= $limit) {
                break;
            }
        }

        return $ids;
    }

    /**
     * Deprecated since 2.1.0: deletes all matching visits within this one request, without preview or confirmation;
     * use previewDeletion() and createJob() with expectedVisits instead.
     *
     * @deprecated since 2.1.0, use createJob() + processJobs()
     * @param int|string $idSite One website ID, a comma-separated list of IDs or "all".
     * @param string $segment Matomo segment definition (required).
     * @param string $dateFrom First day, YYYY-MM-DD.
     * @param string $dateTo Last day, YYYY-MM-DD (inclusive).
     * @param int|string $batchSize Visits per step, 100 to 10000 (default 1000); values outside are raised or lowered
     *                             to these limits.
     * @return array{totalDeleted: int, deletedRows: array<string, int>, batches: int, invalidatedDates: int, idjob: int, status: string}
     *         The finished job: deleted visits, deleted rows per log table, steps, days marked for processing again.
     */
    public function deleteVisits($idSite, $segment, $dateFrom, $dateTo, $batchSize = 1000)
    {
        Piwik::checkUserHasSuperUserAccess();
        @set_time_limit(0);
        @ignore_user_abort(true);

        $job = $this->translated(fn() => $this->factory->create(
            $idSite,
            $segment,
            $dateFrom,
            $dateTo,
            $batchSize,
            $this->defaultBatchSize(),
            Job::SOURCE_API,
            Piwik::getCurrentUserLogin()
        ));
        $this->runner->run(null, $job->idJob);
        $job = $this->requireJob($job->idJob);
        if ($job->status === Job::STATUS_CANCELLED) {
            throw new \Exception(Piwik::translate('FeinwerkDeleteVisitsFilter_ErrorJobCancelled', [$job->idJob]));
        }
        if ($job->status === Job::STATUS_FAILED) {
            throw new \Exception(Piwik::translate('FeinwerkDeleteVisitsFilter_ErrorJobFailed', [$job->idJob, (string) $job->error]));
        }
        if ($job->status !== Job::STATUS_DONE) {
            // queued, running or held: another runner owns the job now
            throw new \Exception(Piwik::translate('FeinwerkDeleteVisitsFilter_ErrorQueuedBehindRunningJob', [$job->idJob]));
        }

        return [
            'totalDeleted' => $job->deletedVisits,
            'deletedRows' => $job->deletedRows,
            'batches' => $job->progress->batches,
            'invalidatedDates' => $job->progress->invalidatedCount(),
            'idjob' => $job->idJob,
            'status' => $job->status,
        ];
    }

    /**
     * Shows what a deletion would remove, without deleting: the matching visits per website, the total and a sample.
     * Use it before createJob() and pass its total as expectedVisits.
     *
     * @param int|string $idSites One website ID, a comma-separated list of IDs or "all".
     * @param string $segment Matomo segment definition (required).
     * @param string $dateFrom First day, YYYY-MM-DD, counted in each website's time zone.
     * @param string $dateTo Last day, YYYY-MM-DD (inclusive), not in the future.
     * @param int|string $sampleSize Number of sample visits, 0 to 100 (default 20).
     * @return array{total: int, action_level: bool, sites: list<array{idsite: int, name: string, count: int}>, sample: list<array>}
     *         total: matching visits; action_level: true when the segment uses page or conversion data (whole visits
     *         are deleted anyway); sites: count per website; sample: visits spread over the websites with matches,
     *         each with idsite, idvisit, visitor_id, time (website time zone, "Y-m-d H:i:s"), ip, browser and os
     *         (Matomo's codes), browser_name and os_name (readable names), device_type, actions, entry_url,
     *         referrer_type and referrer_name.
     */
    public function previewDeletion($idSites, $segment, $dateFrom, $dateTo, $sampleSize = 20)
    {
        Piwik::checkUserHasSuperUserAccess();
        $sampleSize = max(0, min((int) $sampleSize, 100));

        return $this->translated(fn() => $this->preview->build($idSites, $segment, $dateFrom, $dateTo, $sampleSize));
    }

    /**
     * Queues a deletion of every visit that matches the segment in the date range on the given websites; the queue is
     * worked off by processJobs(), the hourly task or the console. Deleted visits cannot be restored: check them with
     * previewDeletion() first and pass its total as expectedVisits, or call with dryRun=1.
     *
     * @param int|string $idSites One website ID, a comma-separated list of IDs or "all".
     * @param string $segment Matomo segment definition (required).
     * @param string $dateFrom First day, YYYY-MM-DD, counted in each website's time zone.
     * @param string $dateTo Last day, YYYY-MM-DD (inclusive), not in the future.
     * @param int|string $batchSize Visits per step, 100 to 10000; values outside are raised or lowered to these
     *                             limits; empty: the plugin's default (setting, 1000).
     * @param string $source "api" (default) or "ui"; only shown in the history and its filter. Other values: HTTP 400.
     * @param int|string $expectedVisits Optional: the number of visits you expect to delete (the total of
     *                                   previewDeletion()). When it differs from the visits that match now, nothing is
     *                                   queued and the call answers HTTP 400 with both numbers. Empty: not checked.
     * @param int|string $dryRun 1: only count, queue nothing (returns dry_run, total, action_level, sites); 0 (default).
     * @return array With dryRun=1: {dry_run: true, total: int, action_level: bool, sites: list<{idsite, name, count}>}.
     *         Otherwise the created job:
     *         The job (`getJob` shape): idjob (int), status (queued, running, held, done, cancelled, failed),
     *         source (ui, api, console, rule), idrule (always null, reserved), idsites (int[]), segment (string),
     *         date_from and date_to (YYYY-MM-DD, days in each website's time zone), batch_size (visits per step),
     *         max_visits (always null, reserved), estimated_visits (int|null, counted when the job starts),
     *         deleted_visits (int), deleted_rows (rows per log table), batches (int), invalidated_dates (int, days
     *         marked for processing again), site_index and sites_total (int, the website being worked on),
     *         messages (list of {key, params}: translation keys, not text), error (string|null), created_by (login),
     *         created_at, started_at, finished_at and heartbeat_at (UTC, "Y-m-d H:i:s", null until set).
     */
    public function createJob($idSites, $segment, $dateFrom, $dateTo, $batchSize = '', $source = 'api', $expectedVisits = '', $dryRun = '0')
    {
        Piwik::checkUserHasSuperUserAccess();
        $source = self::choiceParameter('source', $source, [Job::SOURCE_API, Job::SOURCE_UI]) ?: Job::SOURCE_API;
        $dry = self::flagParameter('dryRun', $dryRun);
        $expected = self::countParameter('expectedVisits', $expectedVisits);
        if ($dry || $expected !== null) {
            $preview = $this->translated(fn() => $this->preview->build($idSites, $segment, $dateFrom, $dateTo, 0));
            if ($expected !== null && $preview['total'] !== $expected) {
                throw self::badRequest(Piwik::translate('FeinwerkDeleteVisitsFilter_ErrorExpectedVisits', [$expected, $preview['total']]));
            }
            if ($dry) {
                return [
                    'dry_run' => true,
                    'total' => $preview['total'],
                    'action_level' => $preview['action_level'],
                    'sites' => $preview['sites'],
                ];
            }
        }
        $job = $this->translated(fn() => $this->factory->create(
            $idSites,
            $segment,
            $dateFrom,
            $dateTo,
            $batchSize,
            $this->defaultBatchSize(),
            $source,
            Piwik::getCurrentUserLogin()
        ));

        return $job->toApiArray();
    }

    /**
     * Works on the queue for up to 15 seconds and returns the jobs still active; call it again while any job is
     * queued or running (the admin page does so every few seconds).
     *
     * @return array{busy: bool, jobs: list<array>} busy: another process (console, hourly task, another page) holds the
     *         runner lock, so this call only reports; jobs: the queued, running and held jobs in the getJob shape.
     */
    public function processJobs()
    {
        Piwik::checkUserHasSuperUserAccess();
        $limit = (int) ini_get('max_execution_time');
        if ($limit !== 0 && $limit < self::PROCESS_BUDGET_SECONDS + MatomoRunnerLock::TTL) {
            @set_time_limit(self::PROCESS_BUDGET_SECONDS + MatomoRunnerLock::TTL);
        }
        $result = $this->runner->run(self::PROCESS_BUDGET_SECONDS);

        return [
            'busy' => $result->busy,
            'jobs' => array_map(fn(Job $job) => $job->toApiArray(), $this->jobs->findActive()),
        ];
    }

    /**
     * Returns one deletion job with its progress and result.
     *
     * @param int|string $idJob The job ID (idjob of createJob() or getJobs()).
     * @return array The job:
     *         The job (`getJob` shape): idjob (int), status (queued, running, held, done, cancelled, failed),
     *         source (ui, api, console, rule), idrule (always null, reserved), idsites (int[]), segment (string),
     *         date_from and date_to (YYYY-MM-DD, days in each website's time zone), batch_size (visits per step),
     *         max_visits (always null, reserved), estimated_visits (int|null, counted when the job starts),
     *         deleted_visits (int), deleted_rows (rows per log table), batches (int), invalidated_dates (int, days
     *         marked for processing again), site_index and sites_total (int, the website being worked on),
     *         messages (list of {key, params}: translation keys, not text), error (string|null), created_by (login),
     *         created_at, started_at, finished_at and heartbeat_at (UTC, "Y-m-d H:i:s", null until set).
     */
    public function getJob($idJob)
    {
        Piwik::checkUserHasSuperUserAccess();

        return $this->requireJob($idJob)->toApiArray();
    }

    /**
     * Lists deletion jobs, newest first: the history of the admin page.
     *
     * @param int|string $offset Number of jobs to skip, 0 or more (default 0).
     * @param int|string $limit Number of jobs, 1 to 500 (default 50).
     * @param string $status Only jobs in this status: queued, running, held, done, cancelled or failed; empty: all.
     * @param string $source Only jobs started this way: ui, api, console or rule; empty: all.
     * @return list<array> Jobs in the getJob shape.
     */
    public function getJobs($offset = 0, $limit = 50, $status = '', $source = '')
    {
        Piwik::checkUserHasSuperUserAccess();
        $status = self::choiceParameter('status', $status, self::STATUSES);
        $source = self::choiceParameter('source', $source, self::SOURCES);
        $jobs = $this->jobs->find(
            max(0, (int) $offset),
            max(1, min((int) $limit, 500)),
            $status,
            $source
        );

        return array_map(fn(Job $job) => $job->toApiArray(), $jobs);
    }

    /**
     * Stops a queued, running or held deletion; visits deleted so far stay deleted.
     *
     * @param int|string $idJob The job ID.
     * @return array The stopped job in the getJob shape (status cancelled).
     */
    public function cancelJob($idJob)
    {
        Piwik::checkUserHasSuperUserAccess();

        return $this->translated(fn() => $this->control->cancel((int) $idJob))->toApiArray();
    }

    /**
     * Queues a failed deletion again; it continues where it stopped.
     *
     * @param int|string $idJob The job ID of a failed job.
     * @return array The job in the getJob shape (status queued).
     */
    public function retryJob($idJob)
    {
        Piwik::checkUserHasSuperUserAccess();

        return $this->translated(fn() => $this->control->retry((int) $idJob))->toApiArray();
    }

    /**
     * One answer to the feedback question under the admin page (thumbs up or down). The Matomo server sends it to
     * Feinwerk (Feedback\PluginFeedback), never the browser; store errors are only logged. One vote per user and question
     * every 10 seconds and at most PluginFeedback::SERVER_CAP votes a minute per server.
     *
     * @param string $topic one of PluginFeedback::TOPICS
     * @param string $vote up or down
     * @return array{sent: bool}
     */
    public function sendFeedback($topic, $vote)
    {
        Piwik::checkUserHasSuperUserAccess();
        if (!PluginFeedback::enabled()) {
            return ['sent' => false];
        }
        try {
            $payload = PluginFeedback::payload(
                trim((string) $topic),
                trim((string) $vote),
                (string) PluginManager::getInstance()->getLoadedPlugin(PluginFeedback::PLUGIN)->getVersion(),
                Version::VERSION,
                (string) ($_SERVER['HTTP_USER_AGENT'] ?? ''),
                (string) StaticContainer::get(Translator::class)->getCurrentLanguage()
            );
        } catch (\InvalidArgumentException $e) {
            throw self::badRequest(Piwik::translate('FeinwerkDeleteVisitsFilter_FeedbackInvalid'), $e);
        }
        $key = PluginFeedback::throttleOption(Piwik::getCurrentUserLogin(), $payload['topic']);
        if (time() - (int) Option::get($key) < 10) {
            return ['sent' => false];
        }
        $server = PluginFeedback::countVote((string) Option::get(PluginFeedback::SERVER_OPTION), time());
        if ($server === null) {
            return ['sent' => false];
        }
        Option::set($key, (string) time());
        Option::set(PluginFeedback::SERVER_OPTION, $server);

        return ['sent' => (new PluginFeedback())->send($payload)];
    }

    /**
     * @return array{idSites: int[], segment: string, range: DateRange}
     */
    private function validate($idSites, $segment, $dateFrom, $dateTo): array
    {
        return $this->translated(fn() => $this->factory->validate($idSites, $segment, $dateFrom, $dateTo));
    }

    /**
     * Runs the operation; invalid input becomes HTTP 400 and an unknown job HTTP 404 (see Input\HttpErrorStatus), each with the
     * translated message.
     *
     * @return mixed
     */
    private function translated(callable $operation)
    {
        try {
            return $operation();
        } catch (InvalidInputException $e) {
            $message = Piwik::translate($e->translationKey, $e->params);
            if ($e->translationKey === 'FeinwerkDeleteVisitsFilter_ErrorJobNotFound') {
                throw self::notFound($message, $e);
            }
            throw self::badRequest($message, $e);
        }
    }

    private static function badRequest(string $message, ?\Throwable $previous = null): \Exception
    {
        return HttpErrorStatus::wanted() ? new BadRequestException($message, $previous) : new \Exception($message, 0, $previous);
    }

    private static function notFound(string $message, ?\Throwable $previous = null): \Exception
    {
        return HttpErrorStatus::wanted() ? new JobNotFoundException($message, $previous) : new \Exception($message, 0, $previous);
    }

    /** An optional enum parameter: '' or one of $allowed, else HTTP 400 naming the parameter and the allowed values. */
    private static function choiceParameter(string $name, $value, array $allowed): string
    {
        $value = RequestValue::text($value);
        if ($value !== '' && !in_array($value, $allowed, true)) {
            throw self::badRequest(Piwik::translate('FeinwerkDeleteVisitsFilter_ErrorInvalidChoice', [$value, $name, implode(', ', $allowed)]));
        }

        return $value;
    }

    /** A 0/1 parameter (also true/false); anything else is HTTP 400. */
    private static function flagParameter(string $name, $value): bool
    {
        $text = strtolower(RequestValue::text($value));
        if (in_array($text, ['1', 'true'], true) || $value === true) {
            return true;
        }
        if (in_array($text, ['', '0', 'false'], true) || $value === false) {
            return false;
        }
        throw self::badRequest(Piwik::translate('FeinwerkDeleteVisitsFilter_ErrorInvalidChoice', [$text, $name, '0, 1']));
    }

    /** An optional count: '' means none, otherwise a whole number of 0 or more, else HTTP 400. */
    private static function countParameter(string $name, $value): ?int
    {
        $text = RequestValue::text($value);
        if ($text === '') {
            return null;
        }
        if (!preg_match('/^\d{1,12}$/', $text)) {
            throw self::badRequest(Piwik::translate('FeinwerkDeleteVisitsFilter_ErrorInvalidNumber', [$text, $name]));
        }

        return (int) $text;
    }

    private function window(int $idSite, DateRange $range): DateWindow
    {
        return DateWindow::forLocalDays($range->from, $range->to, $this->sites->timezone($idSite));
    }

    private function requireJob($idJob): Job
    {
        $job = $this->jobs->get((int) $idJob);
        if ($job === null) {
            throw self::notFound(Piwik::translate('FeinwerkDeleteVisitsFilter_ErrorJobNotFound', [(int) $idJob]));
        }

        return $job;
    }

    private function defaultBatchSize(): int
    {
        return (int) (new SystemSettings())->defaultBatchSize->getValue();
    }
}
