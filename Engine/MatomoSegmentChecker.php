<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine;

use Piwik\Piwik;
use Piwik\Segment;

class MatomoSegmentChecker implements SegmentChecker
{
    public function check(string $segment, int $idSite): ?string
    {
        // Matomo cuts longer definitions off (dropping e.g. a trailing exclusion).
        if (strlen($segment) > Segment::SEGMENT_TRUNCATE_LIMIT) {
            return Piwik::translate('FeinwerkDeleteVisitsFilter_ErrorSegmentTooLong', [Segment::SEGMENT_TRUNCATE_LIMIT]);
        }

        try {
            $parsed = new Segment($segment, [$idSite]);
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        // E.g. "0" is falsy and becomes NO segment, which matches every visit.
        if ($parsed->isEmpty()) {
            return Piwik::translate('FeinwerkDeleteVisitsFilter_ErrorSegmentMatchesEverything');
        }

        return null;
    }
}
