<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Integration;

use Piwik\Concurrency\LockBackend;
use Piwik\Container\StaticContainer;
use Piwik\Db;
use Piwik\Http\HttpCodeException;
use Piwik\NoAccessException;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\API;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Input\BadRequestException;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\JobNotFoundException;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\JobStore;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\MatomoRunnerLock;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Fixtures\DeleteVisitsFixture;
use Piwik\Tests\Framework\Fixture;
use Piwik\Tests\Framework\Mock\FakeAccess;
use Piwik\Tests\Framework\TestCase\IntegrationTestCase;

/**
 * @group FeinwerkDeleteVisitsFilter
 */
class APITest extends IntegrationTestCase
{
    private const BOT = 'visitIp==' . DeleteVisitsFixture::BOT_IP;
    private const HUMAN = 'visitIp==' . DeleteVisitsFixture::HUMAN_IP;

    /** @var DeleteVisitsFixture */
    public static $fixture = null;

    private API $api;

    protected static function configureFixture($fixture)
    {
        parent::configureFixture($fixture);
        // IntegrationTestCase disables real translations; these tests assert English messages.
        $fixture->extraTestEnvVars['loadRealTranslations'] = true;
    }

    public function provideContainerConfig()
    {
        // Without FakeAccess the real Access stays super user and the access test could not fail.
        return ['Piwik\\Access' => new FakeAccess()];
    }

    public function setUp(): void
    {
        parent::setUp();
        // The real translator only knows core strings; add the plugin's lang files.
        Fixture::loadAllTranslations();
        FakeAccess::$superUser = true;
        StaticContainer::get(JobStore::class)->install();
        Db::query('TRUNCATE TABLE ' . JobStore::tableName());
        $this->api = API::getInstance();
    }

    /**
     * @dataProvider provideApiCalls
     */
    public function test_everyMethodRequiresSuperUserAccess(string $method, array $args): void
    {
        FakeAccess::$superUser = false;
        FakeAccess::$idSitesAdmin = [1];

        $this->expectException(NoAccessException::class);
        $this->api->$method(...$args);
    }

    public function provideApiCalls(): array
    {
        $range = [self::BOT, '2026-03-14', '2026-03-14'];

        return [
            'countVisits' => ['countVisits', [1, ...$range]],
            'findVisits' => ['findVisits', [1, ...$range]],
            'deleteVisits' => ['deleteVisits', [1, ...$range]],
            'previewDeletion' => ['previewDeletion', [1, ...$range]],
            'createJob' => ['createJob', [1, ...$range]],
            'processJobs' => ['processJobs', []],
            'getJob' => ['getJob', [1]],
            'getJobs' => ['getJobs', []],
            'cancelJob' => ['cancelJob', [1]],
            'retryJob' => ['retryJob', [1]],
            'sendFeedback' => ['sendFeedback', ['delete_visits', 'up']],
        ];
    }

    public function test_countVisits_usesTheSiteTimezone(): void
    {
        $this->assertSame(2, $this->api->countVisits(1, self::BOT, '2026-03-14', '2026-03-14'));
        $this->assertSame(1, $this->api->countVisits(2, self::BOT, '2026-03-14', '2026-03-14'));
        $this->assertSame(3, $this->api->countVisits('1,2', self::BOT, '2026-03-14', '2026-03-14'));
    }

    public function test_deleteVisits_keepsTheOldResponseShape(): void
    {
        $result = $this->api->deleteVisits(1, self::BOT, '2026-03-14', '2026-03-14', 1000);

        $this->assertSame(2, $result['totalDeleted']);
        $this->assertSame(2, $result['deletedRows']['log_visit']);
        $this->assertSame(1, $result['batches']);
        $this->assertSame(1, $result['invalidatedDates']);
        $this->assertSame('done', $result['status']);
        $this->assertSame(0, $this->api->countVisits(1, self::BOT, '2026-03-14', '2026-03-14'));
        $this->assertSame(1, $this->api->countVisits(1, self::BOT, '2026-03-15', '2026-03-15'));
        $this->assertSame(2, $this->api->countVisits(1, self::HUMAN, '2026-03-14', '2026-03-14'));
    }

    public function test_deleteVisits_reportsTheQueuedJobWhenAnotherRunnerIsActive(): void
    {
        $lock = new MatomoRunnerLock(StaticContainer::get(LockBackend::class));
        $this->assertTrue($lock->acquire());
        $error = null;
        try {
            $this->api->deleteVisits(1, self::BOT, '2026-03-14', '2026-03-14', 1000);
        } catch (\Exception $e) {
            $error = $e;
        } finally {
            $lock->release();
        }

        $this->assertNotNull($error, 'Expected an exception');
        $this->assertStringContainsString('queued as job 1', $error->getMessage());

        $this->assertSame('queued', $this->api->getJob(1)['status']);
        $this->assertSame(2, $this->api->countVisits(1, self::BOT, '2026-03-14', '2026-03-14'));
    }

    public function test_createJobAndProcessJobs_deleteAcrossWebsites(): void
    {
        $idJob = $this->api->createJob('1,2', self::BOT, '2026-03-14', '2026-03-14')['idjob'];

        $result = $this->api->processJobs();

        $this->assertFalse($result['busy']);
        $this->assertSame([], $result['jobs']);
        $job = $this->api->getJob($idJob);
        $this->assertSame('done', $job['status']);
        $this->assertSame('api', $job['source']);
        $this->assertSame(3, $job['deleted_visits']);
        $this->assertSame(2, $this->api->countVisits(1, self::HUMAN, '2026-03-14', '2026-03-14'));
        $this->assertSame(1, $this->api->countVisits(2, self::BOT, '2026-03-15', '2026-03-15'));
    }

    public function test_createJob_rejectsASegmentUnknownForTheWebsites(): void
    {
        $error = null;
        try {
            $this->api->createJob('1,2', 'notASegment==1', '2026-03-14', '2026-03-14');
        } catch (\Exception $e) {
            $error = $e;
        }

        $this->assertNotNull($error, 'Expected an exception');
        $this->assertStringContainsString('UTC shop (1)', $error->getMessage());
        $this->assertStringContainsString('Berlin shop (2)', $error->getMessage());
        $this->assertSame([], $this->api->getJobs());
    }

    public function test_previewDeletion_summarizesPerWebsite(): void
    {
        $preview = $this->api->previewDeletion('1,2', 'pageUrl=@/spam', '2026-03-14', '2026-03-14', 5);

        $this->assertSame(2, $preview['total']);
        $this->assertTrue($preview['action_level']);
        $this->assertSame(['idsite' => 1, 'name' => 'UTC shop', 'count' => 2], $preview['sites'][0]);
        $this->assertSame(0, $preview['sites'][1]['count']);
        $this->assertCount(2, $preview['sample']);
        $this->assertSame(DeleteVisitsFixture::BOT_IP, $preview['sample'][0]['ip']);
        $this->assertStringContainsString('example.com/a', $preview['sample'][0]['entry_url']);
    }

    public function test_sendFeedback_sendsNothingFromATestRun(): void
    {
        // Matomo's test mode switches the feedback off: no vote of a test ever reaches Feinwerk's store.
        $this->assertSame(['sent' => false], $this->api->sendFeedback('delete_visits', 'up'));
    }

    public function test_cancelJobAndRetryJob(): void
    {
        $idJob = $this->api->createJob(1, self::BOT, '2026-03-14', '2026-03-14')['idjob'];

        $this->assertSame('cancelled', $this->api->cancelJob($idJob)['status']);

        $this->expectExceptionMessage('cannot be changed');
        $this->api->retryJob($idJob);
    }

    /** API errors carry HTTP status codes: 404 for an unknown job, 400 for invalid input (Matomo sets the status). */
    public function test_errorsCarryHttpStatusCodes(): void
    {
        foreach ([fn() => $this->api->getJob(987654), fn() => $this->api->cancelJob(987654)] as $call) {
            try {
                $call();
                $this->fail('an unknown job must be refused');
            } catch (JobNotFoundException $e) {
                $this->assertInstanceOf(HttpCodeException::class, $e);
                $this->assertSame(404, $e->getCode());
            }
        }

        try {
            $this->api->createJob(1, self::BOT, '2026-03-14', 'not a date');
            $this->fail('invalid input must be refused');
        } catch (BadRequestException $e) {
            $this->assertInstanceOf(HttpCodeException::class, $e);
            $this->assertSame(400, $e->getCode());
        }
    }

    /** The admin page's own calls (force_api_session=1) get the message with HTTP 200, as before. */
    public function test_adminPageCallsKeepHttp200(): void
    {
        $_POST['force_api_session'] = '1';
        try {
            $this->api->getJob(987654);
            $this->fail('an unknown job must be refused');
        } catch (\Exception $e) {
            $this->assertNotInstanceOf(HttpCodeException::class, $e);
            $this->assertStringContainsString('987654', $e->getMessage());
        } finally {
            unset($_POST['force_api_session']);
        }
    }

    public function test_jsonResponsesKeepNestedFields(): void
    {
        $idJob = $this->api->createJob('1,2', self::BOT, '2026-03-14', '2026-03-14')['idjob'];
        $this->api->processJobs();

        $jobs = json_decode(\Piwik\API\Request::processRequest('FeinwerkDeleteVisitsFilter.getJobs', ['format' => 'json']), true);
        $this->assertSame($idJob, $jobs[0]['idjob']);
        $this->assertSame([1, 2], $jobs[0]['idsites']);
        $this->assertSame(3, $jobs[0]['deleted_rows']['log_visit']);
        $this->assertSame([], $jobs[0]['messages']);

        $preview = json_decode(\Piwik\API\Request::processRequest('FeinwerkDeleteVisitsFilter.previewDeletion', [
            'format' => 'json', 'idSites' => '1', 'segment' => self::HUMAN,
            'dateFrom' => '2026-03-14', 'dateTo' => '2026-03-14', 'sampleSize' => 1,
        ]), true);
        $this->assertSame(2, $preview['total']);
        $this->assertFalse($preview['action_level']);
        $this->assertSame('UTC shop', $preview['sites'][0]['name']);
        $this->assertCount(1, $preview['sample']);
        $this->assertSame(DeleteVisitsFixture::HUMAN_IP, $preview['sample'][0]['ip']);
    }

    public function test_requestValuesWithSurroundingSpaces(): void
    {
        // Why: Matomo 6 passes API request values untrimmed; inputs, the job source and the history filters must
        // still match.
        $this->api->createJob('1', self::HUMAN, '2026-03-14', '2026-03-14');
        $created = json_decode(\Piwik\API\Request::processRequest('FeinwerkDeleteVisitsFilter.createJob', [
            'format' => 'json', 'idSites' => ' 1 ', 'segment' => self::BOT,
            'dateFrom' => ' 2026-03-14 ', 'dateTo' => ' 2026-03-14 ', 'source' => ' ui ',
        ]), true);

        $jobs = json_decode(\Piwik\API\Request::processRequest('FeinwerkDeleteVisitsFilter.getJobs', [
            'format' => 'json', 'status' => ' queued ', 'source' => ' ui ',
        ]), true);
        $this->assertSame([$created['idjob']], array_column($jobs, 'idjob'));
        $this->assertSame([1], $jobs[0]['idsites']);
        $this->assertSame('2026-03-14', $jobs[0]['date_from']);
    }
}

APITest::$fixture = new DeleteVisitsFixture();
