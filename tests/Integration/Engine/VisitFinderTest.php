<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Integration\Engine;

use Piwik\Container\StaticContainer;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\DateWindow;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\VisitFinder;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\VisitSource;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Fixtures\DeleteVisitsFixture;
use Piwik\Segment;
use Piwik\Tests\Framework\TestCase\IntegrationTestCase;

/**
 * @group FeinwerkDeleteVisitsFilter
 */
class VisitFinderTest extends IntegrationTestCase
{
    private const BOT = 'visitIp==' . DeleteVisitsFixture::BOT_IP;

    /** @var DeleteVisitsFixture */
    public static $fixture = null;

    private VisitFinder $finder;

    public function setUp(): void
    {
        parent::setUp();
        $this->finder = new VisitFinder();
    }

    public function test_count_usesTheSiteTimezoneWindow(): void
    {
        $this->assertSame(2, $this->finder->count(1, self::BOT, DateWindow::forLocalDays('2026-03-14', '2026-03-14', 'UTC')));
        $this->assertSame(1, $this->finder->count(2, self::BOT, DateWindow::forLocalDays('2026-03-14', '2026-03-14', 'Europe/Berlin')));
        $this->assertSame(1, $this->finder->count(2, self::BOT, DateWindow::forLocalDays('2026-03-15', '2026-03-15', 'Europe/Berlin')));
    }

    public function test_windows_coverEveryVisitExactlyOnce(): void
    {
        $window = DateWindow::forLocalDays('2026-03-14', '2026-03-15', 'UTC');
        $seen = [];
        $after = null;
        $windows = 0;
        do {
            $until = $this->finder->windowEnd(1, $window, $after, 2);
            foreach ($this->finder->matches(1, 'visitIp!=203.0.113.99', $window, $after, $until) as $visit) {
                $seen[] = $visit['idvisit'];
            }
            $after = $until;
            $windows++;
        } while ($until !== null);

        $this->assertSame(3, $windows);
        sort($seen);
        $this->assertSame(DeleteVisitsFixture::visitIds(1), $seen);
    }

    public function test_matches_supportsActionLevelAndEncodedSegments(): void
    {
        $window = DateWindow::forLocalDays('2026-03-14', '2026-03-14', 'UTC');
        $expected = [
            DeleteVisitsFixture::visitIdAt(1, '2026-03-14 08:01:00'),
            DeleteVisitsFixture::visitIdAt(1, '2026-03-14 11:00:00'),
        ];

        $plain = array_column($this->finder->matches(1, 'pageUrl=@/spam', $window, null, null), 'idvisit');
        $encoded = array_column($this->finder->matches(1, 'pageUrl=@%2Fspam', $window, null, null), 'idvisit');

        $this->assertSame($expected, $plain);
        $this->assertSame($expected, $encoded);
    }

    public function test_matches_returnsTimeOrderedRowsWithTheirTime(): void
    {
        $window = DateWindow::forLocalDays('2026-03-14', '2026-03-14', 'UTC');

        $matches = $this->finder->matches(1, self::BOT, $window, null, null);

        $this->assertSame([
            ['idvisit' => DeleteVisitsFixture::visitIdAt(1, '2026-03-14 08:01:00'), 'time' => '2026-03-14 08:01:00'],
            ['idvisit' => DeleteVisitsFixture::visitIdAt(1, '2026-03-14 09:00:00'), 'time' => '2026-03-14 09:00:00'],
        ], $matches);
    }

    public function test_isActionLevel(): void
    {
        $window = DateWindow::forLocalDays('2026-03-14', '2026-03-14', 'UTC');

        $this->assertTrue($this->finder->isActionLevel(1, 'pageUrl=@/spam', $window));
        $this->assertFalse($this->finder->isActionLevel(1, self::BOT, $window));
    }

    public function test_findIds_returnsTheFirstMatchesInTimeOrder(): void
    {
        $window = DateWindow::forLocalDays('2026-03-14', '2026-03-15', 'UTC');

        $this->assertSame([
            DeleteVisitsFixture::visitIdAt(1, '2026-03-14 08:01:00'),
            DeleteVisitsFixture::visitIdAt(1, '2026-03-14 09:00:00'),
        ], $this->finder->findIds(1, self::BOT, $window, 2));
        $this->assertCount(3, $this->finder->findIds(1, self::BOT, $window, 10));
    }

    public function test_count_supportsActionLevelSegments(): void
    {
        $window = DateWindow::forLocalDays('2026-03-14', '2026-03-14', 'UTC');

        $this->assertSame(2, $this->finder->count(1, 'pageUrl=@/spam', $window));
        $this->assertSame(2, $this->finder->count(1, 'pageUrl!@/spam', $window));
    }

    public function test_matches_negatedActionSegmentExcludesVisitsThatHitThePage(): void
    {
        // Visit 08:01 has /a AND /spam, visit 11:00 only /spam: a plain NOT LIKE join would wrongly keep the first one.
        $window = DateWindow::forLocalDays('2026-03-14', '2026-03-14', 'UTC');

        $matches = $this->finder->matches(1, 'pageUrl!@/spam', $window, null, null);

        $this->assertSame([
            DeleteVisitsFixture::visitIdAt(1, '2026-03-14 09:00:00'),
            DeleteVisitsFixture::visitIdAt(1, '2026-03-14 10:01:00'),
        ], array_column($matches, 'idvisit'));
    }

    public function test_windows_coverActionLevelMatchesExactlyOnce(): void
    {
        $window = DateWindow::forLocalDays('2026-03-14', '2026-03-15', 'UTC');
        $cases = [
            'pageUrl!@/spam' => ['2026-03-14 09:00:00', '2026-03-14 10:01:00', '2026-03-15 09:00:00'],
            'pageUrl=@/a;visitIp!=' . DeleteVisitsFixture::BOT_IP . ',pageUrl=@/b' => ['2026-03-14 10:01:00'],
        ];

        foreach ($cases as $segment => $lastActionTimes) {
            $seen = [];
            $after = null;
            do {
                $until = $this->finder->windowEnd(1, $window, $after, 2);
                foreach ($this->finder->matches(1, $segment, $window, $after, $until) as $visit) {
                    $seen[] = $visit['idvisit'];
                }
                $after = $until;
            } while ($until !== null);

            $expected = array_map(fn(string $time) => DeleteVisitsFixture::visitIdAt(1, $time), $lastActionTimes);
            sort($seen);
            sort($expected);
            $this->assertSame($expected, $seen, $segment);
        }
    }

    public function test_containerResolvesTheVisitSourceToTheFinder(): void
    {
        $this->assertInstanceOf(VisitFinder::class, StaticContainer::get(VisitSource::class));
    }

    /**
     * Matomo silently rewrites these definitions into a different segment ("0" is falsy and becomes NO segment, anything
     * past Segment::SEGMENT_TRUNCATE_LIMIT is cut off, dropping e.g. a trailing exclusion). Deleting on that basis would hit visits
     * the user never selected, so the finder must refuse them.
     *
     * @return array<string, array{0: string}>
     */
    public static function rewrittenSegments(): array
    {
        return [
            'zero is no segment' => ['0'],
            'blank is no segment' => ['  '],
            // The cut falls right behind a ";", so the truncated segment is still valid and the exclusion is silently lost.
            'over the length limit' => [
                'pageUrl=@' . str_repeat('x', Segment::SEGMENT_TRUNCATE_LIMIT - strlen('pageUrl=@') - 1)
                    . ';visitIp!=' . DeleteVisitsFixture::HUMAN_IP,
            ],
        ];
    }

    /**
     * @dataProvider rewrittenSegments
     */
    public function test_count_rejectsSegmentsMatomoWouldRewrite(string $segment): void
    {
        $window = DateWindow::forLocalDays('2026-03-14', '2026-03-15', 'UTC');

        $this->expectException(\InvalidArgumentException::class);
        $this->finder->count(1, $segment, $window);
    }

    /**
     * @dataProvider rewrittenSegments
     */
    public function test_matches_rejectsSegmentsMatomoWouldRewrite(string $segment): void
    {
        $window = DateWindow::forLocalDays('2026-03-14', '2026-03-15', 'UTC');

        $this->expectException(\InvalidArgumentException::class);
        $this->finder->matches(1, $segment, $window, null, null);
    }

    /**
     * @dataProvider rewrittenSegments
     */
    public function test_isActionLevel_rejectsSegmentsMatomoWouldRewrite(string $segment): void
    {
        $window = DateWindow::forLocalDays('2026-03-14', '2026-03-15', 'UTC');

        $this->expectException(\InvalidArgumentException::class);
        $this->finder->isActionLevel(1, $segment, $window);
    }

    public function test_segmentAtTheLengthLimitIsStillAccepted(): void
    {
        $window = DateWindow::forLocalDays('2026-03-14', '2026-03-15', 'UTC');
        $segment = 'pageUrl=@' . str_repeat('x', Segment::SEGMENT_TRUNCATE_LIMIT - strlen('pageUrl=@'));
        $this->assertSame(Segment::SEGMENT_TRUNCATE_LIMIT, strlen($segment));

        $this->assertSame(0, $this->finder->count(1, $segment, $window));
    }

    /**
     * Site 3 has three visits with the identical visit_last_action_time plus a later one: only the (time, idvisit) cursor
     * keeps such a tie from being skipped or returned twice at a window border.
     *
     * @return array<string, array{0: int, 1: int}>
     */
    public static function tieWindowSizes(): array
    {
        // [window size, number of windows (the last one is the open-ended rest)]
        return [
            'one visit per window' => [1, 5],
            'two visits per window' => [2, 3],
            'three visits per window' => [3, 2],
            'all visits in one window' => [10, 1],
        ];
    }

    /**
     * @dataProvider tieWindowSizes
     */
    public function test_windows_returnVisitsWithIdenticalTimesExactlyOnce(int $size, int $expectedWindows): void
    {
        $window = DateWindow::forLocalDays('2026-03-14', '2026-03-14', 'UTC');
        $allIds = DeleteVisitsFixture::visitIds(3);
        $this->assertCount(4, $allIds);
        $this->assertSame(
            [DeleteVisitsFixture::visitIdAt(3, '2026-03-14 13:00:00')],
            array_slice($allIds, 3),
            'the three tied visits come first, the later one last'
        );

        $seen = [];
        $after = null;
        $windows = 0;
        do {
            $until = $this->finder->windowEnd(3, $window, $after, $size);
            foreach ($this->finder->matches(3, 'visitIp!=203.0.113.99', $window, $after, $until) as $visit) {
                $seen[] = $visit['idvisit'];
            }
            $after = $until;
            $windows++;
        } while ($until !== null);

        $this->assertSame($allIds, $seen, 'every visit exactly once, in (time, idvisit) order');
        $this->assertSame($expectedWindows, $windows);
    }

    public function test_windowEnd_ordersVisitsWithIdenticalTimesByIdvisit(): void
    {
        $window = DateWindow::forLocalDays('2026-03-14', '2026-03-14', 'UTC');
        $ids = DeleteVisitsFixture::visitIds(3);

        $first = $this->finder->windowEnd(3, $window, null, 1);
        $second = $this->finder->windowEnd(3, $window, $first, 1);

        $this->assertSame([$ids[0], '2026-03-14 12:00:00'], [$first->idVisit, $first->time]);
        $this->assertSame([$ids[1], '2026-03-14 12:00:00'], [$second->idVisit, $second->time]);
    }

    public function test_windows_coverNegatedActionMatchesWithIdenticalTimesExactlyOnce(): void
    {
        // The segment's own date bounds follow each window; visits sharing the boundary time must stay inside them.
        $window = DateWindow::forLocalDays('2026-03-14', '2026-03-14', 'UTC');
        $all = DeleteVisitsFixture::visitIds(3);
        sort($all);
        // Every visit of site 3 hit /a: the negation of /spam keeps all of them, the negation of /a none. The second
        // case fails if a bound excludes a visit sharing the window's boundary time (the NOT IN subquery then misses it).
        $cases = ['pageUrl!@/spam' => $all, 'pageUrl!@/a' => []];

        foreach ($cases as $segment => $expected) {
            foreach ([1, 2, 3] as $size) {
                $seen = [];
                $after = null;
                do {
                    $until = $this->finder->windowEnd(3, $window, $after, $size);
                    foreach ($this->finder->matches(3, $segment, $window, $after, $until) as $visit) {
                        $seen[] = $visit['idvisit'];
                    }
                    $after = $until;
                } while ($until !== null);

                sort($seen);
                $this->assertSame($expected, $seen, "segment {$segment}, window size {$size}");
            }
        }
    }
}

VisitFinderTest::$fixture = new DeleteVisitsFixture();
