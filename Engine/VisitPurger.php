<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine;

interface VisitPurger
{
    /**
     * Deletes the given visits of one website from all log tables.
     *
     * @param list<array{idvisit: int, time: string}> $visits
     */
    public function purge(int $idSite, array $visits): PurgeResult;
}
