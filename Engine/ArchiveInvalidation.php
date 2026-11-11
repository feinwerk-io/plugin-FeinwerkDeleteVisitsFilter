<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine;

interface ArchiveInvalidation
{
    /**
     * Marks reports of the given local days (YYYY-MM-DD, site timezone) for re-processing.
     *
     * @param string[] $localDays
     */
    public function invalidate(int $idSite, array $localDays): void;
}
