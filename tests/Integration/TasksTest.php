<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Integration;

use Piwik\Container\StaticContainer;
use Piwik\Db;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\Job;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\JobFactory;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\JobStore;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\SystemSettings;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Tasks;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Fixtures\DeleteVisitsFixture;
use Piwik\Tests\Framework\Mock\FakeAccess;
use Piwik\Tests\Framework\TestCase\IntegrationTestCase;

/**
 * @group FeinwerkDeleteVisitsFilter
 */
class TasksTest extends IntegrationTestCase
{
    /** @var DeleteVisitsFixture */
    public static $fixture = null;

    public function setUp(): void
    {
        parent::setUp();
        FakeAccess::$superUser = true;
        StaticContainer::get(JobStore::class)->install();
        Db::query('TRUNCATE TABLE ' . JobStore::tableName());
    }

    private function queue(): Job
    {
        return StaticContainer::get(JobFactory::class)->create(1, 'visitIp==' . DeleteVisitsFixture::BOT_IP, '2026-03-14', '2026-03-14', '', 1000, Job::SOURCE_API, null);
    }

    public function test_processQueue_runsQueuedJobs(): void
    {
        $job = $this->queue();

        (new Tasks())->processQueue();

        $this->assertSame(Job::STATUS_DONE, StaticContainer::get(JobStore::class)->get($job->idJob)->status);
    }

    public function test_processQueue_doesNothingWhenBackgroundProcessingIsOff(): void
    {
        $settings = new SystemSettings();
        $settings->backgroundTimeBudget->setValue(0);
        $settings->save();
        $job = $this->queue();

        (new Tasks())->processQueue();

        $this->assertSame(Job::STATUS_QUEUED, StaticContainer::get(JobStore::class)->get($job->idJob)->status);
    }

    /**
     * @dataProvider invalidBudgets
     */
    public function test_settings_rejectInvalidBudgets($value): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('FeinwerkDeleteVisitsFilter_SettingBackgroundBudgetInvalid');

        (new SystemSettings())->backgroundTimeBudget->setValue($value);
    }

    public function invalidBudgets(): array
    {
        return [['abc'], [''], [null], ['x60'], ['60.5'], [30], [3301], ['-5']];
    }

    /**
     * @dataProvider validBudgets
     */
    public function test_settings_acceptValidBudgets($value): void
    {
        $setting = (new SystemSettings())->backgroundTimeBudget;
        $setting->setValue($value);

        $this->assertSame((int) $value, $setting->getValue());
    }

    public function validBudgets(): array
    {
        return [[0], [60], [3300], ['300']];
    }

    /**
     * @dataProvider invalidBatchSizes
     */
    public function test_settings_rejectInvalidBatchSizes($value): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('FeinwerkDeleteVisitsFilter_SettingDefaultBatchSizeInvalid');

        (new SystemSettings())->defaultBatchSize->setValue($value);
    }

    public function invalidBatchSizes(): array
    {
        return [['abc'], [''], [null], [99], [10001], ['100.5']];
    }

    public function test_settings_acceptBatchSizeBounds(): void
    {
        $setting = (new SystemSettings())->defaultBatchSize;
        $setting->setValue(100);
        $this->assertSame(100, $setting->getValue());
        $setting->setValue('10000');
        $this->assertSame(10000, $setting->getValue());
    }
}

TasksTest::$fixture = new DeleteVisitsFixture();
