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
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Input\DateRange;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Input\InvalidInputException;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Input\RequestValue;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\DeletionPreview;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\Job;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\JobControl;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\JobFactory;
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
     * Number of visits matching the segment within the date range (days in each website's timezone).
     *
     * @param int|string $idSite one id, a comma-separated list or "all"
     * @param string $segment
     * @param string $dateFrom YYYY-MM-DD
     * @param string $dateTo YYYY-MM-DD (inclusive)
     * @return int
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
     * @deprecated since 2.1.0, use previewDeletion()
     * @return int[]
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
     * Deletes all matching visits within this request.
     *
     * @deprecated since 2.1.0, use createJob() + processJobs()
     * @return array
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
     * Matches per website, total, a sample of matching visits and whether the segment uses action-level data.
     *
     * @return array
     */
    public function previewDeletion($idSites, $segment, $dateFrom, $dateTo, $sampleSize = 20)
    {
        Piwik::checkUserHasSuperUserAccess();
        $sampleSize = max(0, min((int) $sampleSize, 100));

        return $this->translated(fn() => $this->preview->build($idSites, $segment, $dateFrom, $dateTo, $sampleSize));
    }

    /**
     * Queues a deletion job. It is processed by processJobs(), the hourly task or the console.
     *
     * @param string $source "ui" or "api"
     * @return array
     */
    public function createJob($idSites, $segment, $dateFrom, $dateTo, $batchSize = '', $source = 'api')
    {
        Piwik::checkUserHasSuperUserAccess();
        $source = RequestValue::text($source) === Job::SOURCE_UI ? Job::SOURCE_UI : Job::SOURCE_API;
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

        return ['idjob' => $job->idJob];
    }

    /**
     * Works on the queue for a few seconds and returns the active jobs. Call repeatedly while jobs are active.
     *
     * @return array
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
     * @return array
     */
    public function getJob($idJob)
    {
        Piwik::checkUserHasSuperUserAccess();

        return $this->requireJob($idJob)->toApiArray();
    }

    /**
     * @return array newest first
     */
    public function getJobs($offset = 0, $limit = 50, $status = '', $source = '')
    {
        Piwik::checkUserHasSuperUserAccess();
        $jobs = $this->jobs->find(
            max(0, (int) $offset),
            max(1, min((int) $limit, 500)),
            RequestValue::text($status),
            RequestValue::text($source)
        );

        return array_map(fn(Job $job) => $job->toApiArray(), $jobs);
    }

    /**
     * @return array
     */
    public function cancelJob($idJob)
    {
        Piwik::checkUserHasSuperUserAccess();

        return $this->translated(fn() => $this->control->cancel((int) $idJob))->toApiArray();
    }

    /**
     * Re-queues a failed job; it continues where it stopped.
     *
     * @return array
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
            throw new \Exception(Piwik::translate('FeinwerkDeleteVisitsFilter_FeedbackInvalid'));
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
     * @return mixed
     */
    private function translated(callable $operation)
    {
        try {
            return $operation();
        } catch (InvalidInputException $e) {
            throw new \Exception(Piwik::translate($e->translationKey, $e->params), 0, $e);
        }
    }

    private function window(int $idSite, DateRange $range): DateWindow
    {
        return DateWindow::forLocalDays($range->from, $range->to, $this->sites->timezone($idSite));
    }

    private function requireJob($idJob): Job
    {
        $job = $this->jobs->get((int) $idJob);
        if ($job === null) {
            throw new \Exception(Piwik::translate('FeinwerkDeleteVisitsFilter_ErrorJobNotFound', [(int) $idJob]));
        }

        return $job;
    }

    private function defaultBatchSize(): int
    {
        return (int) (new SystemSettings())->defaultBatchSize->getValue();
    }
}
