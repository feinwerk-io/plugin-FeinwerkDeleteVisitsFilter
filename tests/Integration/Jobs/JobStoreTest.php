<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Integration\Jobs;

use Piwik\Db;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\FeinwerkDeleteVisitsFilter;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\Job;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\JobRepository;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\JobStore;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Contract\JobRepositoryContract;
use Piwik\Tests\Framework\TestCase\IntegrationTestCase;

/**
 * @group FeinwerkDeleteVisitsFilter
 */
class JobStoreTest extends IntegrationTestCase
{
    use JobRepositoryContract;

    private JobStore $store;

    public function setUp(): void
    {
        parent::setUp();
        $this->store = new JobStore();
        $this->store->install();
        Db::query('TRUNCATE TABLE ' . JobStore::tableName());
    }

    protected function repository(): JobRepository
    {
        return $this->store;
    }

    public function test_install_isIdempotent(): void
    {
        $this->store->install();
        $this->store->install();

        // Inlined like Matomo core does: SHOW ... LIKE cannot take a bound parameter with the MYSQLI adapter.
        $this->assertSame(JobStore::tableName(), Db::fetchOne("SHOW TABLES LIKE '" . JobStore::tableName() . "'"));
    }

    public function test_pluginReportsTheTableAsInstalled(): void
    {
        $tables = [];
        (new FeinwerkDeleteVisitsFilter())->getTablesInstalled($tables);

        $this->assertSame([JobStore::tableName()], $tables);
    }

    public function test_deletingAUserAnonymizesTheirJobs(): void
    {
        $job = Job::create(Job::SOURCE_UI, [1], 'visitIp==1.2.3.4', '2026-03-01', '2026-03-02', 1000, 'peter', '2026-03-20 10:00:00');
        $this->store->insert($job);

        (new FeinwerkDeleteVisitsFilter())->onUserDeleted('peter');

        $this->assertNull($this->store->get($job->idJob)->createdBy);
    }
}
