<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs;

use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\Cursor;

final class JobProgress
{
    /** Index into Job::$idSites of the website being processed. */
    public int $siteIndex = 0;

    /** Number of deletion batches executed so far. */
    public int $batches = 0;

    /** @var array<int, Cursor> idsite => end of the last completely processed window */
    private array $cursors = [];

    /**
     * Per website the last counted local day and the number of distinct days counted. Days of one website arrive in
     * non-decreasing order (windows are ordered by time and visits only ever move forward), so this stays small
     * however long the date range is.
     *
     * @var array<int, array{last: string, count: int}>
     */
    private array $invalidated = [];

    public function cursorFor(int $idSite): ?Cursor
    {
        return $this->cursors[$idSite] ?? null;
    }

    public function setCursor(int $idSite, Cursor $cursor): void
    {
        $this->cursors[$idSite] = $cursor;
    }

    /**
     * Counts local days (for display) and returns those not counted before, sorted.
     *
     * @param string[] $days
     * @return string[]
     */
    public function addInvalidatedDays(int $idSite, array $days): array
    {
        $days = array_values(array_unique($days));
        sort($days);
        $last = $this->invalidated[$idSite]['last'] ?? null;
        $new = array_values(array_filter($days, fn(string $day) => $last === null || $day > $last));
        if ($new !== []) {
            $this->invalidated[$idSite] = [
                'last' => end($new),
                'count' => ($this->invalidated[$idSite]['count'] ?? 0) + count($new),
            ];
        }

        return $new;
    }

    public function invalidatedCount(): int
    {
        return array_sum(array_column($this->invalidated, 'count'));
    }

    public function toArray(): array
    {
        return [
            'site_index' => $this->siteIndex,
            'batches' => $this->batches,
            'cursors' => (object) array_map(fn(Cursor $cursor) => $cursor->toArray(), $this->cursors),
            'invalidated' => (object) $this->invalidated,
        ];
    }

    public static function fromArray(array $data): self
    {
        $progress = new self();
        $progress->siteIndex = (int) ($data['site_index'] ?? 0);
        $progress->batches = (int) ($data['batches'] ?? 0);
        foreach (($data['cursors'] ?? []) as $idSite => $cursor) {
            $progress->cursors[(int) $idSite] = Cursor::fromArray($cursor);
        }
        foreach (($data['invalidated'] ?? []) as $idSite => $entry) {
            if (isset($entry['last'], $entry['count'])) {
                $progress->invalidated[(int) $idSite] = ['last' => (string) $entry['last'], 'count' => (int) $entry['count']];
            } elseif (is_array($entry) && $entry !== []) {
                // Format of earlier development builds: list of day strings.
                $days = array_values(array_unique(array_map('strval', $entry)));
                $progress->invalidated[(int) $idSite] = ['last' => max($days), 'count' => count($days)];
            }
        }

        return $progress;
    }
}
