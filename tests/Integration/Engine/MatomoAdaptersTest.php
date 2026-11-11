<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Integration\Engine;

use Piwik\Access;
use Piwik\Common;
use Piwik\Concurrency\LockBackend;
use Piwik\Container\StaticContainer;
use Piwik\Db;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\MatomoSegmentChecker;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\MatomoSiteDirectory;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\MatomoRunnerLock;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Fixtures\DeleteVisitsFixture;
use Piwik\Segment;
use Piwik\Tests\Framework\TestCase\IntegrationTestCase;

/**
 * @group FeinwerkDeleteVisitsFilter
 */
class MatomoAdaptersTest extends IntegrationTestCase
{
    /** @var DeleteVisitsFixture */
    public static $fixture = null;

    public function test_siteDirectory(): void
    {
        $sites = new MatomoSiteDirectory();

        $this->assertSame([1, 2, 3], $sites->allIds());
        $this->assertTrue($sites->exists(2));
        $this->assertFalse($sites->exists(99));
        $this->assertSame('Europe/Berlin', $sites->timezone(2));
        $this->assertSame('UTC shop', $sites->name(1));
    }

    public function test_segmentChecker(): void
    {
        $checker = new MatomoSegmentChecker();

        $this->assertNull($checker->check('visitIp==1.2.3.4', 1));
        $this->assertIsString($checker->check('notASegment==1', 1));
    }

    public function test_segmentChecker_rejectsSegmentsMatomoWouldRewrite(): void
    {
        $checker = new MatomoSegmentChecker();

        // "0" is falsy: Matomo turns it into NO segment, i.e. every visit would match.
        $this->assertStringContainsString('FeinwerkDeleteVisitsFilter_ErrorSegmentMatchesEverything', (string) $checker->check('0', 1));
        $this->assertStringContainsString('FeinwerkDeleteVisitsFilter_ErrorSegmentMatchesEverything', (string) $checker->check('  ', 1));

        // Matomo cuts segments at SEGMENT_TRUNCATE_LIMIT characters, silently dropping the rest.
        // (the cut falls right behind a ";", so the truncated segment would still parse and the exclusion would be lost)
        $tooLong = 'pageUrl=@' . str_repeat('x', Segment::SEGMENT_TRUNCATE_LIMIT - strlen('pageUrl=@') - 1)
            . ';visitIp!=' . DeleteVisitsFixture::HUMAN_IP;
        $this->assertStringContainsString('FeinwerkDeleteVisitsFilter_ErrorSegmentTooLong', (string) $checker->check($tooLong, 1));
    }

    public function test_segmentChecker_acceptsASegmentAtTheLengthLimit(): void
    {
        $checker = new MatomoSegmentChecker();
        $segment = 'pageUrl=@' . str_repeat('x', Segment::SEGMENT_TRUNCATE_LIMIT - strlen('pageUrl=@'));

        $this->assertNull($checker->check($segment, 1));
        $this->assertNotNull($checker->check($segment . 'x', 1));
    }

    public function test_runnerLock_isExclusive(): void
    {
        $backend = StaticContainer::get(LockBackend::class);
        $a = new MatomoRunnerLock($backend);
        $b = new MatomoRunnerLock($backend);

        $this->assertTrue($a->acquire());
        $this->assertFalse($b->acquire());
        $this->assertTrue($a->extend());
        $this->assertTrue($a->extend(), 'extending twice within one second must not fail');
        $a->release();
        $this->assertTrue($b->acquire());
        $b->release();
    }

    public function test_siteDirectory_readsCurrentDecodedNamesWithoutAccessCheck(): void
    {
        $sites = new MatomoSiteDirectory();
        $this->assertSame('UTC shop', $sites->name(1));

        // Matomo stores names HTML-encoded (as entered in the admin UI); changed behind the directory's back.
        Db::query('UPDATE ' . Common::prefixTable('site') . ' SET name = ? WHERE idsite = 1', ['Shop &amp; Co']);
        Access::getInstance()->setSuperUserAccess(false);
        try {
            $this->assertSame('Shop & Co', $sites->name(1));
            $this->assertSame('UTC', $sites->timezone(1));
        } finally {
            Access::getInstance()->setSuperUserAccess(true);
        }
    }
}

MatomoAdaptersTest::$fixture = new DeleteVisitsFixture();
