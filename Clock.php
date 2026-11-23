<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter;

interface Clock
{
    /** Current Unix timestamp. */
    public function now(): int;

    public function sleep(int $seconds): void;
}
