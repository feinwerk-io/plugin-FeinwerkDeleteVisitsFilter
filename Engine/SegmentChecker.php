<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine;

interface SegmentChecker
{
    /** Returns an error message if the segment cannot be used for the website, otherwise null. */
    public function check(string $segment, int $idSite): ?string;
}
