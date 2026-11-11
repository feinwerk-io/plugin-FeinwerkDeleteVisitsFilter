<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs;

use Piwik\Log\LoggerInterface;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Clock;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\ArchiveInvalidation;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\DateWindow;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\SiteDirectory;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\TransientDatabaseException;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\VisitFinder;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\VisitPurger;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\VisitSource;

/**
 * Works off the job queue under the global runner lock and within a time budget. Used by the admin UI (short
 * budget per request), the hourly task and the console. Deleting is idempotent per window: log_visit goes last, so
 * a window interrupted half-way is simply scanned again.
 */
final class JobRunner
{
    public const EVENT_FINISHED = 'FeinwerkDeleteVisitsFilter.jobFinished';
    public const EVENT_FAILED = 'FeinwerkDeleteVisitsFilter.jobFailed';
    public const EVENT_HELD = 'FeinwerkDeleteVisitsFilter.jobHeld';

    /** Budgets up to this many seconds (admin page: 15) do not start a new job in the second half. */
    private const SHORT_BUDGET_SECONDS = 60;

    private const MAX_TRANSIENT_RETRIES = 3;

    private const FINISHED = 'finished';   // the job left the running state: continue with the next job
    private const PAUSED = 'paused';       // time budget used up
    private const LOCK_LOST = 'lock_lost'; // someone else may run now: stop immediately

    public function __construct(
        private JobRepository $jobs,
        private VisitSource $source,
        private VisitPurger $purger,
        private ArchiveInvalidation $invalidation,
        private SiteDirectory $sites,
        private RunnerLock $lock,
        private Clock $clock,
        private EventDispatcher $events,
        private LoggerInterface $logger,
        private int $windowSize = VisitFinder::WINDOW_SIZE
    ) {
    }

    /**
     * @param int|null      $budgetSeconds null = no time limit
     * @param int|null      $onlyJob       process only this job (console, deprecated API)
     * @param callable|null $onProgress    called with the Job after every deleted batch
     */
    public function run(?int $budgetSeconds, ?int $onlyJob = null, ?callable $onProgress = null): RunResult
    {
        if (!$this->lock->acquire()) {
            return new RunResult(true, []);
        }

        $deadline = $budgetSeconds === null ? null : $this->clock->now() + $budgetSeconds;
        $processed = [];
        try {
            do {
                $job = $onlyJob === null
                    ? $this->jobs->claimNext($this->nowUtc())
                    : $this->jobs->claim($onlyJob, $this->nowUtc());
                if ($job === null) {
                    break;
                }
                $processed[] = $job->idJob;
                $outcome = $this->process($job, $deadline, $onProgress);
            } while (
                $outcome === self::FINISHED
                && $onlyJob === null
                && !$this->deadlineReached($deadline)
                && $this->enoughBudgetForAnotherJob($deadline, $budgetSeconds)
            );
        } finally {
            $this->lock->release();
        }

        return new RunResult(false, array_values(array_unique($processed)));
    }

    private function process(Job $job, ?int $deadline, ?callable $onProgress): string
    {
        // Only real work (a deleted batch or a scanned window) counts against the deadline check, so every call
        // makes progress even if counting alone used up the budget.
        $didWork = false;
        try {
            if ($job->estimatedVisits === null) {
                $estimate = $this->estimate($job);
                if ($estimate === null) {
                    return self::LOCK_LOST; // nothing persisted: the new lock holder starts over
                }
                $job->estimatedVisits = $estimate;
                if (!$this->jobs->saveProgress($job, $this->nowUtc())) {
                    return self::FINISHED;
                }
            }
            // Checked on every call, not only after counting: a crash between saving the estimate and holding the
            // job must not let a retry bypass the limit.
            if ($job->maxVisits !== null && $job->estimatedVisits > $job->maxVisits) {
                if ($this->jobs->transition($job->idJob, [Job::STATUS_RUNNING], Job::STATUS_HELD)) {
                    $this->postWithJob(self::EVENT_HELD, $job->idJob);
                }

                return self::FINISHED;
            }

            $siteCount = count($job->idSites);
            while ($job->progress->siteIndex < $siteCount) {
                $idSite = $job->idSites[$job->progress->siteIndex];
                if (!$this->sites->exists($idSite)) {
                    $job->addMessage('FeinwerkDeleteVisitsFilter_MessageSiteSkipped', [$idSite]);
                    $job->progress->siteIndex++;
                    if (!$this->jobs->saveProgress($job, $this->nowUtc())) {
                        return self::FINISHED;
                    }
                    continue;
                }

                $timezone = $this->sites->timezone($idSite);
                $window = DateWindow::forLocalDays($job->dateFrom, $job->dateTo, $timezone);
                $after = $job->progress->cursorFor($idSite);
                $until = $this->retrying(fn() => $this->source->windowEnd($idSite, $window, $after, $this->windowSize));
                $matches = $this->retrying(fn() => $this->source->matches($idSite, $job->segment, $window, $after, $until));

                foreach (array_chunk($matches, $job->batchSize) as $chunk) {
                    if ($didWork && $this->deadlineReached($deadline)) {
                        return self::PAUSED; // the window is scanned again next time; deleted visits are gone by then
                    }
                    if (!$this->isRunning($job->idJob)) {
                        return self::FINISHED;
                    }
                    if (!$this->lock->extend()) {
                        return self::LOCK_LOST; // confirm we still own the queue before deleting anything
                    }
                    $this->deleteChunk($job, $idSite, $timezone, $chunk);
                    $didWork = true;
                    if (!$this->jobs->saveProgress($job, $this->nowUtc())) {
                        return self::FINISHED;
                    }
                    if ($onProgress !== null) {
                        $onProgress($job);
                    }
                }

                if (!$this->lock->extend()) {
                    return self::LOCK_LOST; // do not advance the cursor without the lock
                }
                if ($until === null) {
                    $job->progress->siteIndex++;
                } else {
                    $job->progress->setCursor($idSite, $until);
                }
                $didWork = true;
                if (!$this->jobs->saveProgress($job, $this->nowUtc())) {
                    return self::FINISHED;
                }
                if ($job->progress->siteIndex < $siteCount && $this->deadlineReached($deadline)) {
                    return self::PAUSED;
                }
            }

            if ($this->jobs->transition($job->idJob, [Job::STATUS_RUNNING], Job::STATUS_DONE, ['finished_at' => $this->nowUtc()])) {
                $this->postWithJob(self::EVENT_FINISHED, $job->idJob);
            }

            return self::FINISHED;
        } catch (\Throwable $e) {
            $this->logger->error('FeinwerkDeleteVisitsFilter: job {job} failed: {error}', [
                'job' => $job->idJob,
                'error' => $e->getMessage(),
                'exception' => $e,
            ]);
            if (!$this->lock->extend()) {
                return self::LOCK_LOST; // the new lock holder decides about this job
            }
            $failed = $this->jobs->transition($job->idJob, [Job::STATUS_RUNNING], Job::STATUS_FAILED, [
                'error' => mb_strcut($e->getMessage(), 0, 60000, 'UTF-8'),
                'finished_at' => $this->nowUtc(),
            ]);
            if ($failed) {
                $this->postWithJob(self::EVENT_FAILED, $job->idJob);
            }

            return self::FINISHED;
        }
    }

    /**
     * @return int|null null when the lock was lost while counting
     */
    private function estimate(Job $job): ?int
    {
        $total = 0;
        foreach ($job->idSites as $idSite) {
            if (!$this->sites->exists($idSite)) {
                continue;
            }
            $window = DateWindow::forLocalDays($job->dateFrom, $job->dateTo, $this->sites->timezone($idSite));
            $total += (int) $this->retrying(fn() => $this->source->count($idSite, $job->segment, $window));
            if (!$this->lock->extend()) {
                return null;
            }
        }

        return $total;
    }

    /**
     * @param list<array{idvisit: int, time: string}> $chunk
     */
    private function deleteChunk(Job $job, int $idSite, string $timezone, array $chunk): void
    {
        $days = array_values(array_unique(array_map(
            fn(array $visit) => DateWindow::localDayOf($visit['time'], $timezone),
            $chunk
        )));
        // Distinct days are only counted for display; every batch invalidates its days again (before and after the
        // purge), because archiving may have run in between and would otherwise keep reports from partly deleted logs.
        $job->progress->addInvalidatedDays($idSite, $days);
        // Before deleting: after a crash between both steps the visits would be gone and the days forgotten.
        $this->invalidation->invalidate($idSite, $days);

        $result = $this->retrying(fn() => $this->purger->purge($idSite, $chunk));
        // After deleting: archiving may have run between the first call and the purge and rebuilt reports from rows
        // that were still there. Matomo skips days that are already pending.
        $this->invalidation->invalidate($idSite, $days);
        $job->deletedVisits += $result->deletedVisits;
        $job->addDeletedRows($result->rowsPerTable);
        $job->progress->batches++;

        $this->logger->info('FeinwerkDeleteVisitsFilter: job {job}, site {site}: deleted {count} visits ({total} in total)', [
            'job' => $job->idJob,
            'site' => $idSite,
            'count' => $result->deletedVisits,
            'total' => $job->deletedVisits,
        ]);
    }

    /**
     * @return mixed
     */
    private function retrying(callable $operation)
    {
        for ($attempt = 0; ; $attempt++) {
            try {
                return $operation();
            } catch (TransientDatabaseException $e) {
                if ($attempt >= self::MAX_TRANSIENT_RETRIES) {
                    throw $e;
                }
                $this->logger->warning('FeinwerkDeleteVisitsFilter: retrying after transient database error: {error}', [
                    'error' => $e->getMessage(),
                ]);
                $this->clock->sleep(2 ** $attempt);
            }
        }
    }

    private function isRunning(int $idJob): bool
    {
        $job = $this->jobs->get($idJob);

        return $job !== null && $job->status === Job::STATUS_RUNNING;
    }

    private function postWithJob(string $eventName, int $idJob): void
    {
        $job = $this->jobs->get($idJob);
        if ($job !== null) {
            $this->events->post($eventName, [$job->toApiArray()]);
        }
    }

    /**
     * A new job starts with counting plus at least one batch. With a short budget (the admin page calls with 15 s)
     * and less than half of it left that would overshoot noticeably, so the next call starts it. Longer budgets
     * (hourly task, console) let another job start until the deadline.
     */
    private function enoughBudgetForAnotherJob(?int $deadline, ?int $budgetSeconds): bool
    {
        if ($deadline === null || $budgetSeconds === null || $budgetSeconds > self::SHORT_BUDGET_SECONDS) {
            return true;
        }

        return ($deadline - $this->clock->now()) * 2 >= $budgetSeconds;
    }

    private function deadlineReached(?int $deadline): bool
    {
        return $deadline !== null && $this->clock->now() >= $deadline;
    }

    private function nowUtc(): string
    {
        return gmdate('Y-m-d H:i:s', $this->clock->now());
    }
}
