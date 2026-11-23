<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Unit\Jobs;

use PHPUnit\Framework\TestCase;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\Cursor;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\Job;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\JobProgress;

class JobTest extends TestCase
{
    private function job(): Job
    {
        return Job::create(Job::SOURCE_CONSOLE, [2, 1], 'visitIp==192.0.2.10', '2026-03-01', '2026-03-14', 500, null, '2026-03-20 10:00:00');
    }

    public function test_create_startsQueuedAndEmpty(): void
    {
        $job = $this->job();

        $this->assertNull($job->idJob);
        $this->assertSame(Job::STATUS_QUEUED, $job->status);
        $this->assertSame([2, 1], $job->idSites);
        $this->assertSame(0, $job->deletedVisits);
        $this->assertSame([], $job->deletedRows);
        $this->assertNull($job->estimatedVisits);
        $this->assertSame(0, $job->progress->siteIndex);
        $this->assertNull($job->progress->cursorFor(2));
    }

    public function test_addDeletedRows_sumsPerTable(): void
    {
        $job = $this->job();
        $job->addDeletedRows(['log_visit' => 2, 'log_link_visit_action' => 5]);
        $job->addDeletedRows(['log_visit' => '3', 'myplugin' => 1]);

        $this->assertSame(['log_visit' => 5, 'log_link_visit_action' => 5, 'myplugin' => 1], $job->deletedRows);
    }

    public function test_progress_reportsOnlyNewInvalidatedDays(): void
    {
        $job = $this->job();

        $this->assertSame(['2026-03-01', '2026-03-02'], $job->progress->addInvalidatedDays(1, ['2026-03-01', '2026-03-02']));
        $this->assertSame(['2026-03-03'], $job->progress->addInvalidatedDays(1, ['2026-03-02', '2026-03-03']));
        $this->assertSame(['2026-03-02'], $job->progress->addInvalidatedDays(2, ['2026-03-02']));
        $this->assertSame(4, $job->progress->invalidatedCount());
    }

    public function test_progress_sortsNewDaysAndIgnoresRepeatedOnes(): void
    {
        $progress = $this->job()->progress;

        $this->assertSame(['2026-03-01', '2026-03-02'], $progress->addInvalidatedDays(1, ['2026-03-02', '2026-03-01', '2026-03-02']));
        $this->assertSame([], $progress->addInvalidatedDays(1, ['2026-03-01']));
        $this->assertSame(2, $progress->invalidatedCount());
    }

    public function test_progress_staysSmallForManyDays(): void
    {
        $progress = $this->job()->progress;
        $day = new \DateTimeImmutable('2000-01-01');
        for ($i = 0; $i < 10000; $i++) {
            $progress->addInvalidatedDays($i % 3 + 1, [$day->modify("+$i days")->format('Y-m-d')]);
        }

        $this->assertSame(10000, $progress->invalidatedCount());
        $this->assertLessThan(500, strlen(json_encode($progress->toArray())));
        $this->assertSame(10000, JobProgress::fromArray(json_decode(json_encode($progress->toArray()), true))->invalidatedCount());
    }

    public function test_progress_readsTheListFormatOfEarlierBuilds(): void
    {
        $progress = JobProgress::fromArray(['invalidated' => ['1' => ['2026-03-01', '2026-03-03', '2026-03-03'], '2' => []]]);

        $this->assertSame(2, $progress->invalidatedCount());
        $this->assertSame(['2026-03-04'], $progress->addInvalidatedDays(1, ['2026-03-03', '2026-03-04']));
        $this->assertSame(['invalidated' => ['1' => ['last' => '2026-03-04', 'count' => 3]]], ['invalidated' => (array) $progress->toArray()['invalidated']]);
    }

    public function test_rowRoundTrip_keepsProgress(): void
    {
        $job = $this->job();
        $job->idJob = 7;
        $job->progress->setCursor(2, new Cursor('2026-03-02 10:00:00', 42));
        $job->progress->siteIndex = 1;
        $job->progress->batches = 3;

        $row = $job->toRow() + ['idjob' => '7'];
        $copy = Job::fromRow($row);

        $this->assertSame(7, $copy->idJob);
        $this->assertSame($job->toRow(), $copy->toRow());
        $this->assertSame(42, $copy->progress->cursorFor(2)->idVisit);
    }

    public function test_fromRow_toleratesNullJsonColumns(): void
    {
        $row = $this->job()->toRow();
        $row['deleted_rows'] = null;
        $row['progress'] = null;
        $row['messages'] = null;

        $job = Job::fromRow($row);

        $this->assertSame([], $job->deletedRows);
        $this->assertSame([], $job->messages);
        $this->assertSame(0, $job->progress->siteIndex);
    }

    public function test_toApiArray(): void
    {
        $job = $this->job();
        $job->idJob = 9;
        $job->deletedVisits = 4;
        $job->progress->siteIndex = 1;
        $job->progress->batches = 2;
        $job->progress->addInvalidatedDays(2, ['2026-03-01']);

        $api = $job->toApiArray();

        $this->assertSame(9, $api['idjob']);
        $this->assertSame('queued', $api['status']);
        $this->assertSame([2, 1], $api['idsites']);
        $this->assertSame(4, $api['deleted_visits']);
        $this->assertSame(2, $api['batches']);
        $this->assertSame(1, $api['invalidated_dates']);
        $this->assertSame(1, $api['site_index']);
        $this->assertSame(2, $api['sites_total']);
        $this->assertArrayNotHasKey('progress', $api);
    }
}
