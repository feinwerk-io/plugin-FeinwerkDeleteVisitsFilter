<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs;

interface RunnerLock
{
    public function acquire(): bool;

    /** Extends the lock's lifetime; false if the lock was lost (expired and taken by someone else). */
    public function extend(): bool;

    public function release(): void;
}
