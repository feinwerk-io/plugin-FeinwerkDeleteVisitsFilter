<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Integration\Jobs;

use Piwik\Concurrency\LockBackend;
use Piwik\Container\StaticContainer;
use Piwik\Db;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\API;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\PurgeResult;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\VisitDeleter;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\VisitPurger;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\Job;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\JobFactory;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\JobRunner;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\JobStore;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\MatomoRunnerLock;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Fixtures\DeleteVisitsFixture;
use Piwik\Tests\Framework\TestCase\IntegrationTestCase;

/**
 * Regression (2026-11-23): "MySQL server has gone away" during a deletion left the job running and the runner lock
 * taken for 10 minutes. The whole path with Matomo's container, lock, job store and database: the server closes the
 * connection in the middle of a step, the runner reconnects, finishes the job and frees the lock.
 *
 * @group FeinwerkDeleteVisitsFilter
 */
class JobRunnerReconnectTest extends IntegrationTestCase
{
    private const BOT = 'visitIp==' . DeleteVisitsFixture::BOT_IP;

    /** @var DeleteVisitsFixture */
    public static $fixture = null;

    /** Set by the test: the next purge loses the connection first. */
    public static bool $loseConnection = false;

    public function provideContainerConfig()
    {
        return [
            VisitPurger::class => \Piwik\DI::factory(function () {
                return new class (StaticContainer::get(VisitDeleter::class)) implements VisitPurger {
                    public function __construct(private VisitDeleter $inner)
                    {
                    }

                    public function purge(int $idSite, array $visits): PurgeResult
                    {
                        if (!JobRunnerReconnectTest::$loseConnection) {
                            return $this->inner->purge($idSite, $visits);
                        }
                        JobRunnerReconnectTest::$loseConnection = false;
                        // the server closes the connection after one idle second
                        Db::exec('SET SESSION wait_timeout = 1');
                        sleep(3);

                        // @: the driver may warn about the dead socket; PHPUnit would turn that into an exception
                        return @$this->inner->purge($idSite, $visits);
                    }
                };
            }),
        ];
    }

    public function setUp(): void
    {
        parent::setUp();
        StaticContainer::get(JobStore::class)->install();
        Db::query('TRUNCATE TABLE ' . JobStore::tableName());
        self::$loseConnection = false;
    }

    public function test_run_reconnectsFinishesTheJobAndFreesTheLock(): void
    {
        $job = StaticContainer::get(JobFactory::class)->create(
            '1',
            self::BOT,
            '2026-03-14',
            '2026-03-14',
            '',
            1000,
            Job::SOURCE_API,
            'superUserLogin'
        );
        self::$loseConnection = true;

        StaticContainer::get(JobRunner::class)->run(null, $job->idJob);

        $this->assertFalse(self::$loseConnection, 'the purge lost the connection');
        $done = StaticContainer::get(JobStore::class)->get($job->idJob);
        $this->assertSame(Job::STATUS_DONE, $done->status, (string) $done->error);
        $this->assertSame(2, $done->deletedVisits);
        $this->assertSame(0, API::getInstance()->countVisits(1, self::BOT, '2026-03-14', '2026-03-14'));
        $lock = new MatomoRunnerLock(StaticContainer::get(LockBackend::class));
        $this->assertTrue($lock->acquire(), 'the runner lock was freed');
        $lock->release();
    }
}

JobRunnerReconnectTest::$fixture = new DeleteVisitsFixture();
