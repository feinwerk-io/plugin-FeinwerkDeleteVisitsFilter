<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Fakes;

use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\RunnerLock;

final class FakeRunnerLock implements RunnerLock
{
    public bool $available = true;
    public bool $extendResult = true;
    public int $acquired = 0;
    public int $extended = 0;
    public int $released = 0;
    /** Number of release() calls that fail like a lost database connection before releasing works. */
    public int $releaseFailures = 0;

    public function acquire(): bool
    {
        if (!$this->available) {
            return false;
        }
        $this->acquired++;

        return true;
    }

    public function extend(): bool
    {
        $this->extended++;

        return $this->extendResult;
    }

    public function release(): void
    {
        if ($this->releaseFailures > 0) {
            $this->releaseFailures--;
            throw new \RuntimeException('SQLSTATE[HY000]: General error: 2006 MySQL server has gone away');
        }
        $this->released++;
    }
}
