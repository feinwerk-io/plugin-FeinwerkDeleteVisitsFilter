<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine;

use Piwik\Common;
use Piwik\Date;
use Piwik\Db;
use Piwik\Segment;

/**
 * Finds visits window by window. Each window is bounded by a cheap index scan on log_visit
 * (index_idsite_datetime); the segment is applied inside the window WITHOUT a LIMIT, because Matomo's query builder
 * moves a LIMIT into an ungrouped subquery when the segment joins other tables (a "batch" could then come back short
 * although more matches exist).
 */
class VisitFinder implements VisitSource
{
    public const WINDOW_SIZE = 10000;

    public function count(int $idSite, string $segment, DateWindow $window): int
    {
        [$where, $bind] = $this->where($idSite, $window, null, null);
        $query = $this->segment($segment, $idSite, $window->fromUtc, $window->toUtcExclusive)
            ->getSelectQuery('COUNT(DISTINCT log_visit.idvisit) AS cnt', 'log_visit', $where, $bind);

        return (int) TransientDatabaseException::wrap(fn() => Db::fetchOne($query['sql'], $query['bind']));
    }

    public function windowEnd(int $idSite, DateWindow $window, ?Cursor $after, int $size): ?Cursor
    {
        [$where, $bind] = $this->where($idSite, $window, $after, null);
        $sql = 'SELECT log_visit.visit_last_action_time, log_visit.idvisit'
            . ' FROM ' . Common::prefixTable('log_visit') . ' AS log_visit'
            . ' WHERE ' . $where
            . ' ORDER BY log_visit.visit_last_action_time ASC, log_visit.idvisit ASC'
            . ' LIMIT 1 OFFSET ' . max(0, $size - 1);
        $row = TransientDatabaseException::wrap(fn() => Db::fetchRow($sql, $bind));

        return $row ? new Cursor((string) $row['visit_last_action_time'], (int) $row['idvisit']) : null;
    }

    public function matches(int $idSite, string $segment, DateWindow $window, ?Cursor $after, ?Cursor $until): array
    {
        [$where, $bind] = $this->where($idSite, $window, $after, $until);
        // Matomo bounds its subqueries for negated action conditions with these dates (>= and <= on
        // visit_last_action_time). [after.time, until.time] contains every visit of (after, until], so the result is
        // unchanged, but each window no longer scans the whole date range.
        $segmentFrom = $after !== null ? $after->time : $window->fromUtc;
        $segmentTo = $until !== null ? $until->time : $window->toUtcExclusive;
        $query = $this->segment($segment, $idSite, $segmentFrom, $segmentTo)->getSelectQuery(
            'DISTINCT log_visit.idvisit, log_visit.visit_last_action_time',
            'log_visit',
            $where,
            $bind
        );
        $rows = TransientDatabaseException::wrap(fn() => Db::fetchAll($query['sql'], $query['bind']));

        $visits = array_map(fn(array $row) => [
            'idvisit' => (int) $row['idvisit'],
            'time' => (string) $row['visit_last_action_time'],
        ], $rows);
        // Ordered in PHP: an ORDER BY would have to survive Matomo's subquery rewriting.
        usort($visits, fn(array $a, array $b) => [$a['time'], $a['idvisit']] <=> [$b['time'], $b['idvisit']]);

        return $visits;
    }

    /**
     * First $limit matching visit ids in time order.
     *
     * @return int[]
     */
    public function findIds(int $idSite, string $segment, DateWindow $window, int $limit): array
    {
        $ids = [];
        $after = null;
        do {
            $until = $this->windowEnd($idSite, $window, $after, self::WINDOW_SIZE);
            foreach ($this->matches($idSite, $segment, $window, $after, $until) as $visit) {
                $ids[] = $visit['idvisit'];
                if (count($ids) >= $limit) {
                    return $ids;
                }
            }
            $after = $until;
        } while ($until !== null);

        return $ids;
    }

    /**
     * True if the segment needs action- or conversion-level data.
     */
    public function isActionLevel(int $idSite, string $segment, DateWindow $window): bool
    {
        [$where, $bind] = $this->where($idSite, $window, null, null);
        $query = $this->segment($segment, $idSite, $window->fromUtc, $window->toUtcExclusive)->getSelectQuery('log_visit.idvisit', 'log_visit', $where, $bind);

        return SegmentInspector::touchesActionTables($query['sql']);
    }

    /**
     * @throws \InvalidArgumentException if Matomo would silently turn the definition into a different segment
     */
    private function segment(string $definition, int $idSite, string $fromUtc, string $toUtc): Segment
    {
        // Matomo cuts longer definitions off (dropping e.g. a trailing exclusion); decoding can only shorten a string.
        if (strlen($definition) > Segment::SEGMENT_TRUNCATE_LIMIT) {
            throw new \InvalidArgumentException(
                'The segment is longer than ' . Segment::SEGMENT_TRUNCATE_LIMIT . ' characters; Matomo would truncate it.'
            );
        }

        // Start/end dates let Matomo restrict its action lookups instead of building an unbounded subquery.
        $segment = new Segment($definition, [$idSite], Date::factory($fromUtc), Date::factory($toUtc));

        // E.g. "0" is falsy and becomes NO segment, which matches every visit. Never delete on that basis.
        if ($segment->isEmpty()) {
            throw new \InvalidArgumentException('The segment contains no valid condition and would match every visit.');
        }

        return $segment;
    }

    /**
     * @return array{0: string, 1: list<int|string>}
     */
    private function where(int $idSite, DateWindow $window, ?Cursor $after, ?Cursor $until): array
    {
        $where = 'log_visit.idsite = ? AND log_visit.visit_last_action_time >= ? AND log_visit.visit_last_action_time < ?';
        $bind = [$idSite, $window->fromUtc, $window->toUtcExclusive];
        if ($after !== null) {
            $where .= ' AND (log_visit.visit_last_action_time > ? OR (log_visit.visit_last_action_time = ? AND log_visit.idvisit > ?))';
            array_push($bind, $after->time, $after->time, $after->idVisit);
        }
        if ($until !== null) {
            $where .= ' AND (log_visit.visit_last_action_time < ? OR (log_visit.visit_last_action_time = ? AND log_visit.idvisit <= ?))';
            array_push($bind, $until->time, $until->time, $until->idVisit);
        }

        return [$where, $bind];
    }
}
