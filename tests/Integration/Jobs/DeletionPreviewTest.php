<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Integration\Jobs;

use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\MatomoSegmentChecker;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\MatomoSiteDirectory;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\VisitFinder;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\VisitSampler;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\DeletionPreview;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\JobFactory;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\JobStore;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\SystemClock;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Fixtures\DeleteVisitsFixture;
use Piwik\Tests\Framework\TestCase\IntegrationTestCase;

/**
 * @group FeinwerkDeleteVisitsFilter
 */
class DeletionPreviewTest extends IntegrationTestCase
{
    private const BOT = 'visitIp==' . DeleteVisitsFixture::BOT_IP;

    /** @var DeleteVisitsFixture */
    public static $fixture = null;

    private DeletionPreview $preview;

    public function setUp(): void
    {
        parent::setUp();
        $sites = new MatomoSiteDirectory();
        $finder = new VisitFinder();
        $this->preview = new DeletionPreview(
            new JobFactory($sites, new MatomoSegmentChecker(), new JobStore(), new SystemClock()),
            $finder,
            new VisitSampler($finder),
            $sites
        );
    }

    public function test_build_countsPerSiteInTheSiteTimezone(): void
    {
        $result = $this->preview->build('1,2', self::BOT, '2026-03-14', '2026-03-14', 10);

        $this->assertSame(3, $result['total']);
        $this->assertSame([
            ['idsite' => 1, 'name' => 'UTC shop', 'count' => 2],
            ['idsite' => 2, 'name' => 'Berlin shop', 'count' => 1],
        ], $result['sites']);
    }

    public function test_build_flagsActionLevelSegments(): void
    {
        $this->assertTrue($this->preview->build(1, 'pageUrl=@/spam', '2026-03-14', '2026-03-14', 5)['action_level']);
        $this->assertFalse($this->preview->build(1, self::BOT, '2026-03-14', '2026-03-14', 5)['action_level']);
    }

    public function test_build_limitsTheSampleAcrossSites(): void
    {
        $this->assertCount(2, $this->preview->build('1,2', self::BOT, '2026-03-14', '2026-03-14', 2)['sample']);
        $this->assertCount(3, $this->preview->build('1,2', self::BOT, '2026-03-14', '2026-03-14', 10)['sample']);
        $this->assertSame([], $this->preview->build('1,2', self::BOT, '2026-03-14', '2026-03-14', 0)['sample']);
    }

    public function test_build_sampleCarriesVisitDetails(): void
    {
        $sample = $this->preview->build('1,2', self::BOT, '2026-03-14', '2026-03-14', 10)['sample'];

        $first = $sample[0];
        $this->assertSame(1, $first['idsite']);
        $this->assertSame(DeleteVisitsFixture::BOT_IP, $first['ip']);
        $this->assertSame('2026-03-14 08:01:00', $first['time']);
        $this->assertStringContainsString('example.com/a', $first['entry_url']);
        $this->assertSame(2, $first['actions']);
        $this->assertSame(16, strlen($first['visitor_id']));

        $berlin = $sample[2];
        $this->assertSame(2, $berlin['idsite']);
        $this->assertSame('2026-03-14 23:30:00', $berlin['time']);
    }

    public function test_build_rejectsInvalidInputBeforeQuerying(): void
    {
        $this->expectException(\Piwik\Plugins\FeinwerkDeleteVisitsFilter\Input\InvalidInputException::class);

        $this->preview->build('1,99', self::BOT, '2026-03-14', '2026-03-14', 5);
    }
}

DeletionPreviewTest::$fixture = new DeleteVisitsFixture();
