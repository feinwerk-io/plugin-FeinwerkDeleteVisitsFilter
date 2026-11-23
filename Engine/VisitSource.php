<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine;

interface VisitSource
{
    public function count(int $idSite, string $segment, DateWindow $window): int;

    /**
     * Cursor of the $size-th visit after $after (from the window start when null) in
     * (visit_last_action_time, idvisit) order – ignoring the segment. Null when fewer than $size visits remain.
     */
    public function windowEnd(int $idSite, DateWindow $window, ?Cursor $after, int $size): ?Cursor;

    /**
     * All segment matches in ($after, $until] (up to the window end when $until is null), ordered by time and idvisit.
     *
     * @return list<array{idvisit: int, time: string}>
     */
    public function matches(int $idSite, string $segment, DateWindow $window, ?Cursor $after, ?Cursor $until): array;
}
