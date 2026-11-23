<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine;

final class PurgeResult
{
    /**
     * @param array<string, int> $rowsPerTable
     */
    public function __construct(
        public readonly int $deletedVisits,
        public readonly array $rowsPerTable
    ) {
    }
}
