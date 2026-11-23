<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Contract;

use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\Cursor;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\Job;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\JobRepository;

/**
 * Behaviour every JobRepository must have. Used by the in-memory fake (unit) and JobStore (integration),
 * so the fake the JobRunner tests rely on cannot drift from the real table.
 */
trait JobRepositoryContract
{
    abstract protected function repository(): JobRepository;

    private function newJob(string $source = Job::SOURCE_UI, ?string $createdBy = 'peter'): Job
    {
        return Job::create($source, [1, 3], 'visitIp==192.0.2.10', '2026-03-01', '2026-03-14', 1000, $createdBy, '2026-03-20 10:00:00');
    }

    /**
     * @param Job[] $jobs
     * @return int[]
     */
    private function ids(array $jobs): array
    {
        return array_map(fn(Job $job) => $job->idJob, $jobs);
    }

    public function test_insert_assignsIdsAndRoundTripsAllFields(): void
    {
        $repo = $this->repository();
        $job = $this->newJob();
        $job->maxVisits = 500;
        $job->addDeletedRows(['log_visit' => 3]);
        $job->progress->setCursor(3, new Cursor('2026-03-02 10:00:00', 42));
        $job->progress->addInvalidatedDays(3, ['2026-03-02']);
        $job->addMessage('FeinwerkDeleteVisitsFilter_MessageSiteSkipped', [7]);

        $first = $repo->insert($job);
        $second = $repo->insert($this->newJob());

        $this->assertSame($first, $job->idJob);
        $this->assertSame($first + 1, $second);
        $loaded = $repo->get($first);
        $this->assertSame($first, $loaded->idJob);
        $this->assertSame($job->toRow(), $loaded->toRow());
        $this->assertNull($repo->get(999999));
    }

    public function test_claimNext_returnsNullWithoutWork(): void
    {
        $this->assertNull($this->repository()->claimNext('2026-03-20 11:00:00'));
    }

    public function test_claimNext_claimsTheOldestQueuedJobAndResumesItWhileRunning(): void
    {
        $repo = $this->repository();
        $a = $repo->insert($this->newJob());
        $b = $repo->insert($this->newJob());

        $claimed = $repo->claimNext('2026-03-20 11:00:00');
        $this->assertSame($a, $claimed->idJob);
        $this->assertSame(Job::STATUS_RUNNING, $claimed->status);
        $this->assertSame('2026-03-20 11:00:00', $claimed->startedAt);

        // A running job is an orphan for the next lock holder and is resumed before queued ones.
        $this->assertSame($a, $repo->claimNext('2026-03-20 11:05:00')->idJob);
        $this->assertSame('2026-03-20 11:00:00', $repo->get($a)->startedAt);
        $this->assertSame(Job::STATUS_QUEUED, $repo->get($b)->status);
    }

    public function test_claim_onlyClaimsQueuedOrRunningJobs(): void
    {
        $repo = $this->repository();
        $id = $repo->insert($this->newJob());

        $this->assertSame(Job::STATUS_RUNNING, $repo->claim($id, '2026-03-20 11:00:00')->status);
        $this->assertSame($id, $repo->claim($id, '2026-03-20 11:01:00')->idJob);
        $repo->transition($id, [Job::STATUS_RUNNING], Job::STATUS_DONE);
        $this->assertNull($repo->claim($id, '2026-03-20 11:02:00'));
        $this->assertNull($repo->claim(999999, '2026-03-20 11:02:00'));
    }

    public function test_claim_keepsTheOriginalStartTimeOfARetriedJob(): void
    {
        $repo = $this->repository();
        $id = $repo->insert($this->newJob());
        $repo->claim($id, '2026-03-20 11:00:00');
        $repo->transition($id, [Job::STATUS_RUNNING], Job::STATUS_FAILED, ['error' => 'boom']);
        $repo->transition($id, [Job::STATUS_FAILED], Job::STATUS_QUEUED, ['error' => null]);

        $this->assertSame('2026-03-20 11:00:00', $repo->claim($id, '2026-03-21 09:00:00')->startedAt);
    }

    public function test_transition_onlyFromTheGivenStatuses(): void
    {
        $repo = $this->repository();
        $id = $repo->insert($this->newJob());

        $this->assertFalse($repo->transition($id, [Job::STATUS_RUNNING], Job::STATUS_DONE));
        $this->assertTrue($repo->transition($id, Job::ACTIVE_STATUSES, Job::STATUS_CANCELLED, ['finished_at' => '2026-03-20 12:00:00']));
        $job = $repo->get($id);
        $this->assertSame(Job::STATUS_CANCELLED, $job->status);
        $this->assertSame('2026-03-20 12:00:00', $job->finishedAt);
        $this->assertFalse($repo->transition(999999, [Job::STATUS_CANCELLED], Job::STATUS_QUEUED));
    }

    public function test_transition_canClearFields(): void
    {
        $repo = $this->repository();
        $id = $repo->insert($this->newJob());
        $repo->claim($id, '2026-03-20 11:00:00');
        $repo->transition($id, [Job::STATUS_RUNNING], Job::STATUS_FAILED, ['error' => 'boom', 'finished_at' => '2026-03-20 11:10:00']);

        $this->assertTrue($repo->transition($id, [Job::STATUS_FAILED], Job::STATUS_QUEUED, ['error' => null, 'finished_at' => null]));
        $job = $repo->get($id);
        $this->assertNull($job->error);
        $this->assertNull($job->finishedAt);
    }

    public function test_transition_rejectsColumnsOutsideTheWhitelist(): void
    {
        $repo = $this->repository();
        $id = $repo->insert($this->newJob());

        $this->expectException(\InvalidArgumentException::class);
        $repo->transition($id, [Job::STATUS_QUEUED], Job::STATUS_CANCELLED, ['segment' => 'x']);
    }

    public function test_saveProgress_recordsWorkAndReportsWhetherTheJobStillRuns(): void
    {
        $repo = $this->repository();
        $id = $repo->insert($this->newJob());
        $job = $repo->claim($id, '2026-03-20 11:00:00');
        $job->deletedVisits = 10;
        $job->estimatedVisits = 20;
        $job->addDeletedRows(['log_visit' => 10]);
        $job->progress->siteIndex = 1;

        $this->assertTrue($repo->saveProgress($job, '2026-03-20 11:00:01'));
        // MySQL reports 0 affected rows for identical data – must still count as "running".
        $this->assertTrue($repo->saveProgress($job, '2026-03-20 11:00:01'));
        $loaded = $repo->get($id);
        $this->assertSame(10, $loaded->deletedVisits);
        $this->assertSame(20, $loaded->estimatedVisits);
        $this->assertSame(1, $loaded->progress->siteIndex);
        $this->assertSame('2026-03-20 11:00:01', $loaded->heartbeatAt);

        $repo->transition($id, [Job::STATUS_RUNNING], Job::STATUS_CANCELLED);
        $job->deletedVisits = 12;
        $this->assertFalse($repo->saveProgress($job, '2026-03-20 11:00:02'));
        $this->assertSame(12, $repo->get($id)->deletedVisits, 'work already done must still be recorded');
        $this->assertSame(Job::STATUS_CANCELLED, $repo->get($id)->status);
    }

    public function test_find_filtersAndPagesNewestFirst(): void
    {
        $repo = $this->repository();
        $a = $repo->insert($this->newJob(Job::SOURCE_UI));
        $b = $repo->insert($this->newJob(Job::SOURCE_CONSOLE));
        $c = $repo->insert($this->newJob(Job::SOURCE_UI));
        $repo->transition($b, [Job::STATUS_QUEUED], Job::STATUS_CANCELLED);

        $this->assertSame([$c, $b, $a], $this->ids($repo->find(0, 10)));
        $this->assertSame([$b], $this->ids($repo->find(1, 1)));
        $this->assertSame([$b], $this->ids($repo->find(0, 10, Job::STATUS_CANCELLED)));
        $this->assertSame([$c, $a], $this->ids($repo->find(0, 10, null, Job::SOURCE_UI)));
    }

    public function test_findActive_returnsQueuedRunningAndHeldOldestFirst(): void
    {
        $repo = $this->repository();
        $a = $repo->insert($this->newJob());
        $b = $repo->insert($this->newJob());
        $c = $repo->insert($this->newJob());
        $d = $repo->insert($this->newJob());
        $repo->claim($b, '2026-03-20 11:00:00');
        $repo->transition($c, [Job::STATUS_QUEUED], Job::STATUS_HELD);
        $repo->transition($d, [Job::STATUS_QUEUED], Job::STATUS_CANCELLED);

        $this->assertSame([$a, $b, $c], $this->ids($repo->findActive()));
    }

    public function test_anonymizeCreator_removesOnlyThatLogin(): void
    {
        $repo = $this->repository();
        $a = $repo->insert($this->newJob(Job::SOURCE_UI, 'peter'));
        $b = $repo->insert($this->newJob(Job::SOURCE_UI, 'anna'));

        $repo->anonymizeCreator('peter');

        $this->assertNull($repo->get($a)->createdBy);
        $this->assertSame('anna', $repo->get($b)->createdBy);
    }
}
