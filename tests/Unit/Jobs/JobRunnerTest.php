<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Unit\Jobs;

use PHPUnit\Framework\TestCase;
use Piwik\Log\NullLogger;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\Cursor;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\DateWindow;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\VisitSource;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\Job;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\JobRunner;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Fakes\CallLog;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Fakes\FakeClock;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Fakes\FakeDatabaseConnection;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Fakes\FakeInvalidation;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Fakes\FakePurger;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Fakes\FakeRunnerLock;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Fakes\FakeSiteDirectory;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Fakes\FakeVisitSource;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Fakes\InMemoryJobRepository;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Fakes\RecordingEventDispatcher;

class JobRunnerTest extends TestCase
{
    private InMemoryJobRepository $jobs;
    private FakeClock $clock;
    private CallLog $log;
    private FakeVisitSource $source;
    private FakePurger $purger;
    private FakeInvalidation $invalidation;
    private FakeSiteDirectory $sites;
    private FakeRunnerLock $lock;
    private RecordingEventDispatcher $events;
    private FakeDatabaseConnection $connection;

    protected function setUp(): void
    {
        $this->jobs = new InMemoryJobRepository();
        $this->clock = new FakeClock(1773900000);
        $this->log = new CallLog();
        $this->source = new FakeVisitSource($this->clock);
        $this->purger = new FakePurger($this->source, $this->clock, $this->log);
        $this->invalidation = new FakeInvalidation($this->log);
        $this->sites = new FakeSiteDirectory([1 => 'UTC']);
        $this->lock = new FakeRunnerLock();
        $this->events = new RecordingEventDispatcher();
        $this->connection = new FakeDatabaseConnection();
    }

    private function runner(int $windowSize = 3): JobRunner
    {
        return new JobRunner(
            $this->jobs,
            $this->source,
            $this->purger,
            $this->invalidation,
            $this->sites,
            $this->lock,
            $this->clock,
            $this->events,
            new NullLogger(),
            $this->connection,
            $windowSize
        );
    }

    private function queueJob(array $idSites = [1], int $batchSize = 2, string $from = '2026-03-14', string $to = '2026-03-14', ?int $maxVisits = null): Job
    {
        $job = Job::create(Job::SOURCE_CONSOLE, $idSites, 'visitIp==192.0.2.10', $from, $to, $batchSize, 'peter', '2026-03-20 10:00:00');
        $job->maxVisits = $maxVisits;
        $this->jobs->insert($job);

        return $job;
    }

    /**
     * Visits $firstId … $firstId+$n-1 on $idSite, one per minute from 08:00 UTC of $day.
     *
     * @param int[] $matching ids that match the segment
     */
    private function addVisits(int $idSite, int $n, array $matching, string $day = '2026-03-14', int $firstId = 1): void
    {
        for ($i = 0; $i < $n; $i++) {
            $id = $firstId + $i;
            $this->source->add($idSite, $id, sprintf('%s 08:%02d:00', $day, $i), in_array($id, $matching, true));
        }
    }

    private function job(Job $job): Job
    {
        return $this->jobs->get($job->idJob);
    }

    public function test_run_returnsBusyWhenAnotherProcessHoldsTheLock(): void
    {
        $job = $this->queueJob();
        $this->lock->available = false;

        $result = $this->runner()->run(15);

        $this->assertTrue($result->busy);
        $this->assertSame([], $result->processedJobIds);
        $this->assertSame(Job::STATUS_QUEUED, $this->job($job)->status);
    }

    public function test_run_deletesExactlyTheMatchingVisitsAcrossWindows(): void
    {
        $this->addVisits(1, 10, [2, 3, 5, 9, 10]);
        $job = $this->queueJob();

        $result = $this->runner(3)->run(null);

        $job = $this->job($job);
        $this->assertFalse($result->busy);
        $this->assertSame([$job->idJob], $result->processedJobIds);
        $this->assertSame(Job::STATUS_DONE, $job->status);
        $this->assertSame([2, 3, 5, 9, 10], $this->purger->purgedIds());
        $this->assertSame([1, 4, 6, 7, 8], $this->source->remainingIds(1));
        $this->assertSame(5, $job->estimatedVisits);
        $this->assertSame(5, $job->deletedVisits);
        $this->assertSame(['log_visit' => 5, 'log_link_visit_action' => 10], $job->deletedRows);
        $this->assertSame(4, $job->progress->batches);
        $this->assertNotNull($job->finishedAt);
        $this->assertSame([JobRunner::EVENT_FINISHED], $this->events->names());
        $this->assertSame(1, $this->lock->acquired);
        $this->assertSame(1, $this->lock->released);
    }

    public function test_run_processesSitesInOrderUsingEachSitesTimezone(): void
    {
        $this->sites = new FakeSiteDirectory([1 => 'UTC', 2 => 'Europe/Berlin']);
        $this->source->add(1, 1, '2026-03-14 08:00:00', true);
        $this->source->add(2, 2, '2026-03-14 22:30:00', true); // 23:30 Berlin, 14th → inside
        $this->source->add(2, 3, '2026-03-14 23:30:00', true); // 00:30 Berlin, 15th → outside
        $this->source->add(2, 4, '2026-03-13 23:30:00', true); // 00:30 Berlin, 14th → inside
        $this->queueJob([1, 2]);

        $this->runner()->run(null);

        $this->assertSame([['idsite' => 1, 'ids' => [1]], ['idsite' => 2, 'ids' => [4, 2]]], $this->purger->calls);
        $this->assertSame([3], $this->source->remainingIds(2));
    }

    public function test_run_invalidatesLocalDaysBeforeAndAfterDeleting(): void
    {
        $this->sites = new FakeSiteDirectory([2 => 'Europe/Berlin']);
        $this->source->add(2, 1, '2026-03-13 23:30:00', true); // local 14th
        $this->source->add(2, 2, '2026-03-14 08:00:00', true); // local 14th
        $this->source->add(2, 3, '2026-03-14 23:30:00', true); // local 15th
        $job = $this->queueJob([2], 2, '2026-03-14', '2026-03-15');

        $this->runner(10)->run(null);

        $this->assertSame([
            'invalidate:2:2026-03-14', 'purge:2:1,2', 'invalidate:2:2026-03-14',
            'invalidate:2:2026-03-15', 'purge:2:3', 'invalidate:2:2026-03-15',
        ], $this->log->entries);
        $this->assertSame(2, $this->job($job)->progress->invalidatedCount());
    }

    public function test_run_stopsAtTheTimeBudgetAndResumesLater(): void
    {
        $this->addVisits(1, 6, [1, 2, 3, 4, 5, 6]);
        $this->purger->secondsPerPurge = 10;
        $job = $this->queueJob([1], 2);

        $this->runner(100)->run(15);

        $this->assertSame(Job::STATUS_RUNNING, $this->job($job)->status);
        $this->assertSame([1, 2, 3, 4], $this->purger->purgedIds());

        $this->runner(100)->run(15);

        $this->assertSame(Job::STATUS_DONE, $this->job($job)->status);
        $this->assertSame([1, 2, 3, 4, 5, 6], $this->purger->purgedIds());
        $this->assertSame(6, $this->job($job)->deletedVisits);
    }

    public function test_run_alwaysDeletesAtLeastOneBatchEvenIfCountingUsedTheBudget(): void
    {
        $this->addVisits(1, 4, [1, 2, 3, 4]);
        $this->source->countDelaySeconds = 100;
        $this->queueJob([1], 2);

        $this->runner(100)->run(15);

        $this->assertSame([1, 2], $this->purger->purgedIds());
    }

    public function test_run_stopsWhenTheJobIsCancelledBetweenBatches(): void
    {
        $this->addVisits(1, 6, [1, 2, 3, 4, 5, 6]);
        $job = $this->queueJob([1], 2);
        $this->purger->afterPurge[] = function (int $call) use ($job) {
            if ($call === 1) {
                $this->jobs->transition($job->idJob, [Job::STATUS_RUNNING], Job::STATUS_CANCELLED);
            }
        };

        $this->runner(100)->run(null);

        $this->assertSame([1, 2], $this->purger->purgedIds());
        $this->assertSame(Job::STATUS_CANCELLED, $this->job($job)->status);
        $this->assertSame(2, $this->job($job)->deletedVisits);
        $this->assertSame([], $this->events->names());
    }

    public function test_run_holdsJobsThatExceedTheirMaximum(): void
    {
        $this->addVisits(1, 5, [1, 2, 3, 4, 5]);
        $job = $this->queueJob([1], 2, maxVisits: 4);

        $this->runner()->run(null);

        $this->assertSame(Job::STATUS_HELD, $this->job($job)->status);
        $this->assertSame(5, $this->job($job)->estimatedVisits);
        $this->assertSame([], $this->purger->purgedIds());
        $this->assertSame([JobRunner::EVENT_HELD], $this->events->names());
    }

    public function test_run_skipsWebsitesThatNoLongerExist(): void
    {
        $this->addVisits(1, 2, [1, 2]);
        $job = $this->queueJob([7, 1]);

        $this->runner()->run(null);

        $this->assertSame(Job::STATUS_DONE, $this->job($job)->status);
        $this->assertSame([1, 2], $this->purger->purgedIds());
        $this->assertSame([['key' => 'FeinwerkDeleteVisitsFilter_MessageSiteSkipped', 'params' => [7]]], $this->job($job)->messages);
    }

    public function test_run_retriesTransientDatabaseErrors(): void
    {
        $this->addVisits(1, 2, [1, 2]);
        $this->purger->transientFailures = 2;
        $job = $this->queueJob();

        $this->runner()->run(null);

        $this->assertSame(Job::STATUS_DONE, $this->job($job)->status);
        $this->assertSame([1, 2], $this->purger->purgedIds());
        $this->assertSame([1, 2], $this->clock->sleeps);
    }

    /**
     * Regression (2026-11-23): "MySQL server has gone away" in a step held the runner lock for 10 minutes and left the
     * page waiting. A lost connection is reconnected and the step retried.
     */
    public function test_run_reconnectsAfterALostConnectionAndContinues(): void
    {
        $this->addVisits(1, 2, [1, 2]);
        $this->purger->transientFailures = 1;
        $this->purger->transientMessage = 'SQLSTATE[HY000]: General error: 2006 MySQL server has gone away';
        $job = $this->queueJob();

        $this->runner()->run(null);

        $this->assertSame(Job::STATUS_DONE, $this->job($job)->status);
        $this->assertSame(1, $this->connection->reconnects);
        $this->assertSame([1, 2], $this->purger->purgedIds());
    }

    /** A database restart: the connection is lost, then refused for a moment, then back. */
    public function test_run_retriesWhileTheDatabaseRestarts(): void
    {
        $this->addVisits(1, 2, [1, 2]);
        $this->purger->transientMessages = [
            'SQLSTATE[HY000]: General error: 2006 MySQL server has gone away',
            'SQLSTATE[HY000] [2002] Connection refused',
        ];
        $job = $this->queueJob();

        $this->runner()->run(null);

        $this->assertSame(Job::STATUS_DONE, $this->job($job)->status);
        $this->assertSame(2, $this->connection->reconnects);
        $this->assertSame([1, 2], $this->clock->sleeps);
    }

    /** Not every call goes through TransientDatabaseException::wrap(): a plain driver exception is retried as well. */
    public function test_run_retriesARawLostConnection(): void
    {
        $this->addVisits(1, 2, [1, 2]);
        $this->purger->rawTransient = true;
        $this->purger->transientMessages = ['SQLSTATE[HY000]: General error: 2006 MySQL server has gone away'];
        $job = $this->queueJob();

        $this->runner()->run(null);

        $this->assertSame(Job::STATUS_DONE, $this->job($job)->status);
        $this->assertSame(1, $this->connection->reconnects);
    }

    /** Saving the progress is a database step too: a lost connection there is reconnected and saved again. */
    public function test_run_retriesSavingTheProgressAfterALostConnection(): void
    {
        $this->addVisits(1, 2, [1, 2]);
        $job = $this->queueJob();
        $this->jobs->saveProgressFailures = 1;

        $this->runner()->run(null);

        $this->assertSame(Job::STATUS_DONE, $this->job($job)->status);
        $this->assertSame(1, $this->connection->reconnects);
        $this->assertSame([1, 2], $this->purger->purgedIds());
    }

    /** Other errors are not retried: the job fails at once. */
    public function test_run_doesNotRetryOtherErrors(): void
    {
        $this->addVisits(1, 2, [1, 2]);
        $this->purger->rawTransient = true;
        $this->purger->transientMessages = ["SQLSTATE[42S02]: Base table or view not found: 1146 Table 'log_visit' doesn't exist"];
        $job = $this->queueJob();

        $this->runner()->run(null);

        $this->assertSame(Job::STATUS_FAILED, $this->job($job)->status);
        $this->assertSame(0, $this->connection->reconnects);
        $this->assertSame([], $this->clock->sleeps);
    }

    /** The connection stays away through the retries: reconnect, mark the job failed (retryable) and free the lock. */
    public function test_run_failsAndFreesTheLockWhenTheConnectionStaysLost(): void
    {
        $this->addVisits(1, 2, [1, 2]);
        $this->purger->transientFailures = 4;
        $this->purger->transientMessage = 'SQLSTATE[HY000]: General error: 2013 Lost connection to MySQL server during query';
        $job = $this->queueJob();

        $this->runner()->run(null);

        $this->assertSame(Job::STATUS_FAILED, $this->job($job)->status);
        $this->assertStringContainsString('Lost connection', $this->job($job)->error);
        $this->assertSame(4, $this->connection->reconnects, 'before each retry and once before marking it failed');
        $this->assertSame(1, $this->lock->released);
        $this->assertSame([JobRunner::EVENT_FAILED], $this->events->names());
    }

    public function test_run_freesTheLockAfterReconnectingWhenTheReleaseLostTheConnection(): void
    {
        $this->addVisits(1, 2, [1, 2]);
        $this->lock->releaseFailures = 1;
        $job = $this->queueJob();

        $this->runner()->run(null);

        $this->assertSame(Job::STATUS_DONE, $this->job($job)->status);
        $this->assertSame(1, $this->connection->reconnects);
        $this->assertSame(1, $this->lock->released);
    }

    public function test_run_failsAfterTooManyTransientErrors(): void
    {
        $this->addVisits(1, 2, [1, 2]);
        $this->purger->transientFailures = 4;
        $job = $this->queueJob();

        $this->runner()->run(null);

        $this->assertSame(Job::STATUS_FAILED, $this->job($job)->status);
        $this->assertStringContainsString('Deadlock', $this->job($job)->error);
        $this->assertSame([1, 2, 4], $this->clock->sleeps);
        $this->assertSame([JobRunner::EVENT_FAILED], $this->events->names());
    }

    public function test_run_failsJobsWhoseSegmentTheSourceRefusesWithoutRetrying(): void
    {
        $refusing = new class implements VisitSource {
            public function count(int $idSite, string $segment, DateWindow $window): int
            {
                throw new \InvalidArgumentException('The segment contains no valid condition.');
            }

            public function windowEnd(int $idSite, DateWindow $window, ?Cursor $after, int $size): ?Cursor
            {
                throw new \LogicException('not reached');
            }

            public function matches(int $idSite, string $segment, DateWindow $window, ?Cursor $after, ?Cursor $until): array
            {
                throw new \LogicException('not reached');
            }
        };
        $job = $this->queueJob();
        $runner = new JobRunner(
            $this->jobs,
            $refusing,
            $this->purger,
            $this->invalidation,
            $this->sites,
            $this->lock,
            $this->clock,
            $this->events,
            new NullLogger(),
            $this->connection
        );

        $result = $runner->run(null);

        $this->assertSame([$job->idJob], $result->processedJobIds);
        $this->assertSame(Job::STATUS_FAILED, $this->job($job)->status);
        $this->assertSame('The segment contains no valid condition.', $this->job($job)->error);
        $this->assertSame([], $this->clock->sleeps);
        $this->assertSame([], $this->purger->purgedIds());
        $this->assertSame([JobRunner::EVENT_FAILED], $this->events->names());
        $this->assertSame(1, $this->lock->released);
    }

    public function test_run_marksFailedJobsContinuesAndRetryFinishesTheRest(): void
    {
        $this->addVisits(1, 4, [1, 2, 3, 4]);
        $this->source->add(1, 50, '2026-03-15 08:00:00', true);
        $first = $this->queueJob([1], 2);
        $second = $this->queueJob([1], 2, '2026-03-15', '2026-03-15');
        $this->purger->failWith = new \RuntimeException('listener exploded');
        $this->purger->failOnCall = 2;

        $result = $this->runner(100)->run(null);

        $this->assertSame([$first->idJob, $second->idJob], $result->processedJobIds);
        $this->assertSame(Job::STATUS_FAILED, $this->job($first)->status);
        $this->assertSame('listener exploded', $this->job($first)->error);
        $this->assertSame(Job::STATUS_DONE, $this->job($second)->status);
        $this->assertSame(1, $this->lock->released);

        $this->purger->failWith = null;
        $this->jobs->transition($first->idJob, [Job::STATUS_FAILED], Job::STATUS_QUEUED, ['error' => null, 'finished_at' => null]);
        $this->runner(100)->run(null);

        $this->assertSame(Job::STATUS_DONE, $this->job($first)->status);
        $this->assertSame([], $this->source->remainingIds(1));
    }

    public function test_run_stopsWhenTheLockIsLostAndTheNextRunResumes(): void
    {
        $this->addVisits(1, 4, [1, 2, 3, 4]);
        $job = $this->queueJob([1], 2);
        $this->purger->afterPurge[] = function (int $call) {
            if ($call === 1) {
                $this->lock->extendResult = false;
            }
        };

        $this->runner(100)->run(null);

        $this->assertSame([1, 2], $this->purger->purgedIds());
        $this->assertSame(Job::STATUS_RUNNING, $this->job($job)->status);
        $this->assertSame(1, $this->lock->released);

        $this->lock->extendResult = true;
        $this->runner(100)->run(null);

        $this->assertSame(Job::STATUS_DONE, $this->job($job)->status);
        $this->assertSame([1, 2, 3, 4], $this->purger->purgedIds());
        $this->assertSame(2, $this->lock->released);
    }

    public function test_run_doesNotDeleteOrSaveAnEstimateWhenTheLockIsLostDuringCounting(): void
    {
        $this->addVisits(1, 4, [1, 2, 3, 4]);
        $job = $this->queueJob([1], 2);
        $this->lock->extendResult = false;

        $this->runner(100)->run(null);

        $this->assertSame([], $this->purger->purgedIds());
        $this->assertSame(Job::STATUS_RUNNING, $this->job($job)->status);
        $this->assertNull($this->job($job)->estimatedVisits);
        $this->assertSame(1, $this->lock->released);
    }

    public function test_run_deletesNothingWhenAResumedJobCannotConfirmTheLock(): void
    {
        $this->addVisits(1, 4, [1, 2, 3, 4]);
        $job = $this->queueJob([1], 2);
        $job->estimatedVisits = 4;
        $this->jobs->saveProgress($job, '2026-03-20 10:00:00');
        $this->jobs->claim($job->idJob, '2026-03-20 10:00:00');
        $this->lock->extendResult = false;

        $this->runner(100)->run(null);

        $this->assertSame([], $this->purger->purgedIds());
        $this->assertSame(Job::STATUS_RUNNING, $this->job($job)->status);
    }

    public function test_run_doesNotAdvanceTheCursorWhenTheLockIsLostAfterTheLastBatchOfAWindow(): void
    {
        $this->addVisits(1, 4, [1, 2, 3, 4]);
        $job = $this->queueJob([1], 2);
        $this->purger->afterPurge[] = function (int $call) {
            if ($call === 1) {
                $this->lock->extendResult = false;
            }
        };

        $this->runner(2)->run(null);

        $this->assertSame([1, 2], $this->purger->purgedIds());
        $this->assertNull($this->job($job)->progress->cursorFor(1));
        $this->assertSame(Job::STATUS_RUNNING, $this->job($job)->status);
    }

    public function test_run_doesNotMarkTheJobFailedWhenTheLockIsLostWhileAnErrorOccurs(): void
    {
        $this->addVisits(1, 4, [1, 2, 3, 4]);
        $job = $this->queueJob([1], 2);
        $this->purger->afterPurge[] = function (int $call) {
            $this->lock->extendResult = false;
        };
        $this->purger->failWith = new \RuntimeException('boom');
        $this->purger->failOnCall = 1;

        $this->runner(100)->run(null);

        $this->assertSame(Job::STATUS_RUNNING, $this->job($job)->status);
        $this->assertNull($this->job($job)->error);
        $this->assertSame([], $this->events->names());
    }

    public function test_run_holdsAResumedJobWhoseSavedEstimateExceedsItsMaximum(): void
    {
        $this->addVisits(1, 5, [1, 2, 3, 4, 5]);
        $job = $this->queueJob([1], 2, maxVisits: 4);
        $job->estimatedVisits = 5;
        $this->jobs->saveProgress($job, '2026-03-20 10:00:00');
        $this->jobs->claim($job->idJob, '2026-03-20 10:00:00');

        $this->runner()->run(null);

        $this->assertSame(Job::STATUS_HELD, $this->job($job)->status);
        $this->assertSame([], $this->purger->purgedIds());
        $this->assertSame([JobRunner::EVENT_HELD], $this->events->names());
    }

    public function test_run_invalidatesTheDayAroundEveryBatchButCountsItOnce(): void
    {
        $this->addVisits(1, 4, [1, 2, 3, 4]);
        $job = $this->queueJob([1], 2);

        $this->runner(100)->run(null);

        $this->assertSame(
            [
                'invalidate:1:2026-03-14', 'purge:1:1,2', 'invalidate:1:2026-03-14',
                'invalidate:1:2026-03-14', 'purge:1:3,4', 'invalidate:1:2026-03-14',
            ],
            $this->log->entries
        );
        $this->assertSame(1, $this->job($job)->progress->invalidatedCount());
    }

    public function test_run_stopsWhenTheJobIsCancelledWhileTheWindowIsScanned(): void
    {
        $this->addVisits(1, 4, [1, 2, 3, 4]);
        $job = $this->queueJob([1], 2);
        $this->source->beforeMatches[] = function () use ($job) {
            $this->jobs->transition($job->idJob, [Job::STATUS_RUNNING], Job::STATUS_CANCELLED);
        };

        $this->runner(100)->run(null);

        $this->assertSame([], $this->purger->purgedIds());
        $this->assertSame(Job::STATUS_CANCELLED, $this->job($job)->status);
    }

    public function test_run_truncatesLongMultibyteErrorsToTheByteLimit(): void
    {
        $this->addVisits(1, 2, [1, 2]);
        $job = $this->queueJob();
        $this->purger->failWith = new \RuntimeException('x' . str_repeat('ä', 35000));
        $this->purger->failOnCall = 1;

        $this->runner(100)->run(null);

        $this->assertSame(Job::STATUS_FAILED, $this->job($job)->status);
        $this->assertLessThanOrEqual(60000, strlen($this->job($job)->error));
        $this->assertTrue(mb_check_encoding($this->job($job)->error, 'UTF-8'));
    }

    public function test_run_resumesAnOrphanedRunningJobBeforeQueuedOnes(): void
    {
        $a = $this->queueJob();
        $b = $this->queueJob();
        $this->jobs->claim($b->idJob, '2026-03-20 10:00:00');

        $result = $this->runner()->run(null);

        $this->assertSame([$b->idJob, $a->idJob], $result->processedJobIds);
    }

    public function test_run_withOnlyJob_processesJustThatJob(): void
    {
        $a = $this->queueJob();
        $b = $this->queueJob();

        $result = $this->runner()->run(null, $b->idJob);

        $this->assertSame([$b->idJob], $result->processedJobIds);
        $this->assertSame(Job::STATUS_QUEUED, $this->job($a)->status);
        $this->assertSame(Job::STATUS_DONE, $this->job($b)->status);
    }

    public function test_run_reportsProgressAfterEachBatch(): void
    {
        $this->addVisits(1, 5, [1, 2, 3, 4, 5]);
        $this->queueJob([1], 2);
        $seen = [];

        $this->runner(100)->run(null, null, function (Job $job) use (&$seen) {
            $seen[] = $job->deletedVisits;
        });

        $this->assertSame([2, 4, 5], $seen);
    }

    public function test_run_deletesVisitsThatMoveBehindTheCursorWhileRunning(): void
    {
        $this->addVisits(1, 6, [1, 2, 3, 4, 5, 6]);
        $calls = 0;
        // Visit 2 is still active: before the first window's matches are read it gets a new action at 08:30.
        $this->source->beforeMatches[] = function () use (&$calls) {
            if (++$calls === 1) {
                $this->source->move(1, 2, '2026-03-14 08:30:00');
            }
        };
        $job = $this->queueJob([1], 2);

        $this->runner(3)->run(null);

        $purged = $this->purger->purgedIds();
        sort($purged);
        $this->assertSame([1, 2, 3, 4, 5, 6], $purged);
        $this->assertSame([], $this->source->remainingIds(1));
        $this->assertSame(6, $this->job($job)->deletedVisits);
    }

    public function test_run_doesNotStartAnotherJobWhenLessThanHalfTheBudgetIsLeft(): void
    {
        $this->addVisits(1, 2, [1, 2]);
        $this->source->add(1, 50, '2026-03-15 08:00:00', true);
        $first = $this->queueJob([1], 2);
        $second = $this->queueJob([1], 2, '2026-03-15', '2026-03-15');
        $this->purger->secondsPerPurge = 10;

        $result = $this->runner(100)->run(15);

        $this->assertSame([$first->idJob], $result->processedJobIds);
        $this->assertSame(Job::STATUS_DONE, $this->job($first)->status);
        $this->assertSame(Job::STATUS_QUEUED, $this->job($second)->status);
    }

    public function test_run_startsAnotherJobWithALongBudgetEvenWhenLessThanHalfIsLeft(): void
    {
        $this->addVisits(1, 2, [1, 2]);
        $this->source->add(1, 50, '2026-03-15 08:00:00', true);
        $first = $this->queueJob([1], 2);
        $second = $this->queueJob([1], 2, '2026-03-15', '2026-03-15');
        $this->purger->secondsPerPurge = 200;

        $result = $this->runner(100)->run(300);

        $this->assertSame([$first->idJob, $second->idJob], $result->processedJobIds);
        $this->assertSame(Job::STATUS_DONE, $this->job($first)->status);
        $this->assertNotSame(Job::STATUS_QUEUED, $this->job($second)->status);
    }
}
