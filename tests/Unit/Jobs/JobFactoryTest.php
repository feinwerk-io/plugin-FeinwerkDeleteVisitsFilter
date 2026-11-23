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
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\JobFactory;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Fakes\FakeClock;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Fakes\FakeSegmentChecker;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Fakes\FakeSiteDirectory;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Fakes\InMemoryJobRepository;

class JobFactoryTest extends TestCase
{
    private FakeSiteDirectory $sites;
    private FakeSegmentChecker $segments;
    private InMemoryJobRepository $jobs;

    protected function setUp(): void
    {
        $this->sites = new FakeSiteDirectory([1 => 'UTC', 2 => 'Europe/Berlin', 3 => 'UTC']);
        $this->segments = new FakeSegmentChecker();
        $this->jobs = new InMemoryJobRepository();
    }

    private function factory(): JobFactory
    {
        return new JobFactory($this->sites, $this->segments, $this->jobs, new FakeClock(strtotime('2026-03-20 10:00:00 UTC')));
    }

    private function expectKey(string $key, callable $call): InvalidInputException
    {
        try {
            $call();
        } catch (InvalidInputException $e) {
            $this->assertSame($key, $e->translationKey);

            return $e;
        }
        $this->fail('Expected InvalidInputException ' . $key);
    }

    public function test_create_storesAQueuedJobWithNormalizedInput(): void
    {
        $job = $this->factory()->create('2, 1', ' visitIp==1.2.3.4 ', '2026-03-01', '2026-03-14', '50', 1000, Job::SOURCE_UI, 'peter');

        $this->assertSame(1, $job->idJob);
        $stored = $this->jobs->get(1);
        $this->assertSame(Job::STATUS_QUEUED, $stored->status);
        $this->assertSame([2, 1], $stored->idSites);
        $this->assertSame('visitIp==1.2.3.4', $stored->segment);
        $this->assertSame('2026-03-01', $stored->dateFrom);
        $this->assertSame('2026-03-14', $stored->dateTo);
        $this->assertSame(100, $stored->batchSize);
        $this->assertSame(Job::SOURCE_UI, $stored->source);
        $this->assertSame('peter', $stored->createdBy);
        $this->assertSame('2026-03-20 10:00:00', $stored->createdAt);
    }

    public function test_create_usesTheDefaultBatchSizeWhenEmpty(): void
    {
        $this->assertSame(500, $this->factory()->create(1, 'visitIp==1.2.3.4', '2026-03-01', '2026-03-01', '', 500, Job::SOURCE_API, null)->batchSize);
    }

    public function test_validate_resolvesAllWebsitesSorted(): void
    {
        $this->sites = new FakeSiteDirectory([3 => 'UTC', 1 => 'UTC', 2 => 'UTC']);

        $this->assertSame([1, 2, 3], $this->factory()->validate('all', 'visitIp==1.2.3.4', '2026-03-01', '2026-03-01')['idSites']);
    }

    public function test_validate_rejectsUnknownWebsites(): void
    {
        $e = $this->expectKey('FeinwerkDeleteVisitsFilter_ErrorUnknownSites', fn() => $this->factory()->validate('1,9', 'visitIp==1.2.3.4', '2026-03-01', '2026-03-01'));

        $this->assertSame(['9'], $e->params);
    }

    public function test_validate_rejectsASegmentThatIsInvalidForAnyWebsite(): void
    {
        $this->segments->errors[2] = "Segment 'foo' is not a supported segment.";

        $e = $this->expectKey('FeinwerkDeleteVisitsFilter_ErrorSegmentInvalidForSites', fn() => $this->factory()->validate('1,2', 'foo==1', '2026-03-01', '2026-03-01'));

        $this->assertStringContainsString('Site 2 (2)', $e->params[0]);
        $this->assertStringNotContainsString('Site 1 (1)', $e->params[0]);
    }

    public function test_validate_rejectsDatesInTheFuture(): void
    {
        $this->expectKey('FeinwerkDeleteVisitsFilter_ErrorDateInFuture', fn() => $this->factory()->validate(1, 'visitIp==1.2.3.4', '2026-03-01', '2026-03-22'));
    }

    public function test_create_storesNothingWhenValidationFails(): void
    {
        $this->expectKey('FeinwerkDeleteVisitsFilter_ErrorNoSegment', fn() => $this->factory()->create(1, '', '2026-03-01', '2026-03-01', '', 1000, Job::SOURCE_API, null));

        $this->assertSame([], $this->jobs->find(0, 10));
    }
}
