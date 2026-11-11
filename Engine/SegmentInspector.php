<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine;

final class SegmentInspector
{
    /**
     * True if the SQL Matomo generated for a segment joins action or conversion tables, i.e. the segment matches on
     * action-level data. Deleting such matches still removes the WHOLE visit.
     * Matomo aliases joined tables by their unprefixed name ("… AS log_link_visit_action").
     */
    public static function touchesActionTables(string $sql): bool
    {
        return (bool) preg_match('/\b(log_link_visit_action|log_conversion|log_conversion_item)\b/', $sql);
    }
}
