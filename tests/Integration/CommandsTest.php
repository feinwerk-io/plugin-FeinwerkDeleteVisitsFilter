<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Integration;

use Piwik\Access;
use Piwik\Container\StaticContainer;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\Job;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\JobFactory;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\JobStore;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Fixtures\DeleteVisitsFixture;
use Piwik\Tests\Framework\TestCase\ConsoleCommandTestCase;

/**
 * @group FeinwerkDeleteVisitsFilter
 */
class CommandsTest extends ConsoleCommandTestCase
{
    private const BOT = 'visitIp==' . DeleteVisitsFixture::BOT_IP;

    /** @var DeleteVisitsFixture */
    public static $fixture = null;

    public function setUp(): void
    {
        parent::setUp();

        // Why: plugin.json requires Matomo < 6, so Matomo 6 unloads the plugin and its console commands do not exist.
        if (version_compare(\Piwik\Version::VERSION, '6.0.0-b1', '>=')) {
            $this->markTestSkipped('The plugin requires Matomo < 6 (plugin.json); Matomo 6 does not activate it, so its console commands do not exist there.');
        }

        StaticContainer::get(JobStore::class)->install();
    }

    // Not "run": that name belongs to PHPUnit's TestCase.
    private function runConsole(array $input): int
    {
        return $this->applicationTester->run($input);
    }

    private function display(): string
    {
        return $this->applicationTester->getDisplay();
    }

    // Must run before the test that deletes the fixture's visits (the fixture is shared).
    public function test_delete_nonInteractiveWithoutYesFailsAndDeletesNothing(): void
    {
        $args = ['command' => 'feinwerkdeletevisitsfilter:delete', 'idsite' => '1', 'segment' => self::BOT, 'date-from' => '2026-03-14', 'date-to' => '2026-03-14'];

        $code = $this->applicationTester->run($args, ['interactive' => false]);

        $this->assertSame(1, $code);
        $this->assertStringContainsString('Add --yes to delete without confirmation', $this->display());
        $this->assertCount(3, DeleteVisitsFixture::visitIds(1, DeleteVisitsFixture::BOT_IP));
        $this->assertSame([], StaticContainer::get(JobStore::class)->find(0, 10));
    }

    public function test_delete_dryRunThenDelete(): void
    {
        $args = ['command' => 'feinwerkdeletevisitsfilter:delete', 'idsite' => '1', 'segment' => self::BOT, 'date-from' => '2026-03-14', 'date-to' => '2026-03-14'];

        $this->assertSame(0, $this->runConsole($args + ['--dry-run' => true]), $this->getCommandDisplayOutputErrorMessage());
        $this->assertStringContainsString('Total: 2 matching visits.', $this->display());
        $this->assertCount(3, DeleteVisitsFixture::visitIds(1, DeleteVisitsFixture::BOT_IP));

        $this->assertSame(0, $this->runConsole($args + ['--yes' => true]), $this->getCommandDisplayOutputErrorMessage());
        $this->assertStringContainsString('2 visits deleted', $this->display());
        $this->assertCount(1, DeleteVisitsFixture::visitIds(1, DeleteVisitsFixture::BOT_IP));
    }

    public function test_delete_oldCommandNameStillWorks(): void
    {
        $code = $this->runConsole(['command' => 'deletevisitsfilter:delete', 'idsite' => '2', 'segment' => self::BOT, 'date-from' => '2026-03-14', 'date-to' => '2026-03-14', '--dry-run' => true]);

        $this->assertSame(0, $code, $this->getCommandDisplayOutputErrorMessage());
        $this->assertStringContainsString('Total: 1 matching visits.', $this->display());
    }

    public function test_delete_reportsInvalidInput(): void
    {
        $code = $this->runConsole(['command' => 'feinwerkdeletevisitsfilter:delete', 'idsite' => '1', 'segment' => self::BOT, 'date-from' => '2026-03-14', 'date-to' => '2026-03-01', '--dry-run' => true]);

        $this->assertSame(1, $code);
        $this->assertStringContainsString('must not be after', $this->display());
    }

    public function test_workAndJobs(): void
    {
        // The factory validates the segment against the current user; the test process has none (the console command runs as super user).
        $job = Access::doAsSuperUser(fn() => StaticContainer::get(JobFactory::class)->create(2, self::BOT, '2026-03-15', '2026-03-15', '', 1000, Job::SOURCE_API, null));

        $this->assertSame(0, $this->runConsole(['command' => 'feinwerkdeletevisitsfilter:work']), $this->getCommandDisplayOutputErrorMessage());
        $this->assertStringContainsString('Processed 1 job(s).', $this->display());

        $this->assertSame(0, $this->runConsole(['command' => 'feinwerkdeletevisitsfilter:jobs']), $this->getCommandDisplayOutputErrorMessage());
        $this->assertStringContainsString('done', $this->display());
        $this->assertSame(Job::STATUS_DONE, StaticContainer::get(JobStore::class)->get($job->idJob)->status);
    }
}

CommandsTest::$fixture = new DeleteVisitsFixture();
