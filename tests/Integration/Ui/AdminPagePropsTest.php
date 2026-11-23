<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Integration\Ui;

use Piwik\Common;
use Piwik\Container\StaticContainer;
use Piwik\Db;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\Job;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\JobRepository;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\JobStore;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Fixtures\DeleteVisitsFixture;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Ui\AdminPageProps;
use Piwik\Tests\Framework\TestCase\IntegrationTestCase;

/**
 * @group FeinwerkDeleteVisitsFilter
 */
class AdminPagePropsTest extends IntegrationTestCase
{
    /** @var DeleteVisitsFixture */
    public static $fixture = null;

    public function setUp(): void
    {
        parent::setUp();
        StaticContainer::get(JobStore::class)->install();
        Db::query('TRUNCATE TABLE ' . JobStore::tableName());
    }

    public function test_build_listsDecodedSiteNamesAndFallsBackToTheFirstWebsite(): void
    {
        Db::query('UPDATE ' . Common::prefixTable('site') . ' SET name = ? WHERE idsite = 2', ['Berlin &amp; Co']);

        $props = StaticContainer::get(AdminPageProps::class)->build(99);

        $this->assertSame(1, $props['initialSiteId']);
        $this->assertEquals((object) [1 => 'UTC shop', 2 => 'Berlin & Co', 3 => 'Tie shop'], $props['siteNames']);
        $this->assertSame('{"1":"UTC shop","2":"Berlin & Co","3":"Tie shop"}', json_encode($props['siteNames']));
        $this->assertSame([], $props['activeJobs']);
        $this->assertSame(10000, $props['confirmThreshold']);
        $this->assertSame(300, $props['backgroundBudget']);
        $this->assertSame(1000, $props['defaultBatchSize']);
        $this->assertFalse($props['feedback']['enabled'], 'never in a test run');
        $this->assertStringStartsWith('https://feinwerk.io/', $props['feedback']['supportUrl']);
        $this->assertNotSame('', $props['feedback']['version']);
    }

    public function test_build_keepsTheRequestedWebsiteAndListsActiveJobs(): void
    {
        $job = Job::create(Job::SOURCE_UI, [2], 'visitIp==192.0.2.10', '2026-03-14', '2026-03-14', 1000, 'peter', '2026-10-06 10:00:00');
        StaticContainer::get(JobRepository::class)->insert($job);

        $props = StaticContainer::get(AdminPageProps::class)->build(2);

        $this->assertSame(2, $props['initialSiteId']);
        $this->assertCount(1, $props['activeJobs']);
        $this->assertSame('queued', $props['activeJobs'][0]['status']);
        $this->assertSame([2], $props['activeJobs'][0]['idsites']);
    }
}

AdminPagePropsTest::$fixture = new DeleteVisitsFixture();
