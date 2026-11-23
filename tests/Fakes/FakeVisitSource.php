<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Fakes;

use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\Cursor;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\DateWindow;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\VisitSource;

/**
 * Visits in memory; "match" stands in for the segment. Mirrors VisitFinder's window semantics.
 */
final class FakeVisitSource implements VisitSource
{
    /** @var array<int, array<int, array{idvisit: int, time: string, match: bool}>> */
    private array $visits = [];

    /** Seconds the clock advances per count() – simulates slow counting. */
    public int $countDelaySeconds = 0;

    /** @var list<callable(): void> run before every matches() call */
    public array $beforeMatches = [];

    public function __construct(private FakeClock $clock)
    {
    }

    public function add(int $idSite, int $idVisit, string $time, bool $match): void
    {
        $this->visits[$idSite][$idVisit] = ['idvisit' => $idVisit, 'time' => $time, 'match' => $match];
    }

    public function remove(int $idVisit): void
    {
        foreach (array_keys($this->visits) as $idSite) {
            unset($this->visits[$idSite][$idVisit]);
        }
    }

    public function move(int $idSite, int $idVisit, string $time): void
    {
        $this->visits[$idSite][$idVisit]['time'] = $time;
    }

    /** @return int[] ascending */
    public function remainingIds(int $idSite): array
    {
        $ids = array_keys($this->visits[$idSite] ?? []);
        sort($ids);

        return $ids;
    }

    public function count(int $idSite, string $segment, DateWindow $window): int
    {
        $this->clock->advance($this->countDelaySeconds);

        return count(array_filter($this->ordered($idSite, $window, null, null), fn(array $visit) => $visit['match']));
    }

    public function windowEnd(int $idSite, DateWindow $window, ?Cursor $after, int $size): ?Cursor
    {
        $visits = $this->ordered($idSite, $window, $after, null);
        if (count($visits) < $size) {
            return null;
        }

        return new Cursor($visits[$size - 1]['time'], $visits[$size - 1]['idvisit']);
    }

    public function matches(int $idSite, string $segment, DateWindow $window, ?Cursor $after, ?Cursor $until): array
    {
        foreach ($this->beforeMatches as $hook) {
            $hook();
        }
        $matching = array_filter($this->ordered($idSite, $window, $after, $until), fn(array $visit) => $visit['match']);

        return array_values(array_map(fn(array $visit) => ['idvisit' => $visit['idvisit'], 'time' => $visit['time']], $matching));
    }

    /**
     * @return list<array{idvisit: int, time: string, match: bool}>
     */
    private function ordered(int $idSite, DateWindow $window, ?Cursor $after, ?Cursor $until): array
    {
        $visits = array_filter($this->visits[$idSite] ?? [], function (array $visit) use ($window, $after, $until) {
            if ($visit['time'] < $window->fromUtc || $visit['time'] >= $window->toUtcExclusive) {
                return false;
            }
            $position = [$visit['time'], $visit['idvisit']];
            if ($after !== null && ($position <=> [$after->time, $after->idVisit]) <= 0) {
                return false;
            }
            if ($until !== null && ($position <=> [$until->time, $until->idVisit]) > 0) {
                return false;
            }

            return true;
        });
        usort($visits, fn(array $a, array $b) => [$a['time'], $a['idvisit']] <=> [$b['time'], $b['idvisit']]);

        return $visits;
    }
}
