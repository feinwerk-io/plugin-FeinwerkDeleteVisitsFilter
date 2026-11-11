<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs;

final class RunResult
{
    /**
     * @param int[] $processedJobIds
     */
    public function __construct(
        public readonly bool $busy,
        public readonly array $processedJobIds
    ) {
    }
}
