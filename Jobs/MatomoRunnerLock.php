<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs;

use Piwik\Concurrency\Lock;
use Piwik\Concurrency\LockBackend;

/**
 * One runner at a time for the whole Matomo instance. The TTL frees the lock of a crashed process.
 */
class MatomoRunnerLock implements RunnerLock
{
    public const TTL = 600;

    private Lock $lock;

    public function __construct(LockBackend $backend)
    {
        $this->lock = new Lock($backend, 'FwDvfRunner');
    }

    public function acquire(): bool
    {
        return $this->lock->acquireLock('queue', self::TTL);
    }

    public function extend(): bool
    {
        return $this->lock->extendLock(self::TTL);
    }

    public function release(): void
    {
        $this->lock->unlock();
    }
}
