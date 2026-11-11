<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Unit\Jobs;

use PHPUnit\Framework\TestCase;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Input\InvalidInputException;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\Job;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\JobControl;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Fakes\FakeClock;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Fakes\InMemoryJobRepository;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Fakes\RecordingEventDispatcher;

class JobControlTest extends TestCase
{
    private InMemoryJobRepository $jobs;
    private RecordingEventDispatcher $events;
    private JobControl $control;

    protected function setUp(): void
    {
        $this->jobs = new InMemoryJobRepository();
        $this->events = new RecordingEventDispatcher();
        $this->control = new JobControl($this->jobs, $this->events, new FakeClock(strtotime('2026-03-20 10:00:00 UTC')));
    }

    private function insert(): int
    {
        return $this->jobs->insert(Job::create(Job::SOURCE_UI, [1], 'visitIp==1.2.3.4', '2026-03-01', '2026-03-01', 1000, 'peter', '2026-03-20 09:00:00'));
    }

    public function test_cancel_cancelsActiveJobsAndPostsAnEvent(): void
    {
        $id = $this->insert();

        $job = $this->control->cancel($id);

        $this->assertSame(Job::STATUS_CANCELLED, $job->status);
        $this->assertSame('2026-03-20 10:00:00', $job->finishedAt);
        $this->assertSame([JobControl::EVENT_CANCELLED], $this->events->names());
    }

    public function test_cancel_refusesFinishedJobs(): void
    {
        $id = $this->insert();
        $this->jobs->claim($id, '2026-03-20 09:30:00');
        $this->jobs->transition($id, [Job::STATUS_RUNNING], Job::STATUS_DONE);

        try {
            $this->control->cancel($id);
            $this->fail('Expected InvalidInputException');
        } catch (InvalidInputException $e) {
            $this->assertSame('FeinwerkDeleteVisitsFilter_ErrorJobStateChange', $e->translationKey);
            $this->assertSame([$id, 'done'], $e->params);
        }
    }

    public function test_cancel_unknownJob(): void
    {
        $this->expectException(InvalidInputException::class);
        $this->expectExceptionMessage('FeinwerkDeleteVisitsFilter_ErrorJobNotFound');

        $this->control->cancel(999);
    }

    public function test_retry_requeuesFailedJobs(): void
    {
        $id = $this->insert();
        $this->jobs->claim($id, '2026-03-20 09:30:00');
        $this->jobs->transition($id, [Job::STATUS_RUNNING], Job::STATUS_FAILED, ['error' => 'boom', 'finished_at' => '2026-03-20 09:31:00']);

        $job = $this->control->retry($id);

        $this->assertSame(Job::STATUS_QUEUED, $job->status);
        $this->assertNull($job->error);
        $this->assertNull($job->finishedAt);
    }

    public function test_retry_refusesJobsThatDidNotFail(): void
    {
        $id = $this->insert();

        $this->expectException(InvalidInputException::class);
        $this->expectExceptionMessage('FeinwerkDeleteVisitsFilter_ErrorJobStateChange');

        $this->control->retry($id);
    }
}
