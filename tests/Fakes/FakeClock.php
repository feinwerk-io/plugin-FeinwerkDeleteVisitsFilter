<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Fakes;

use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Clock;

final class FakeClock implements Clock
{
    /** @var int[] */
    public array $sleeps = [];

    public function __construct(public int $now = 0)
    {
    }

    public function now(): int
    {
        return $this->now;
    }

    public function sleep(int $seconds): void
    {
        $this->sleeps[] = $seconds;
        $this->now += $seconds;
    }

    public function advance(int $seconds): void
    {
        $this->now += $seconds;
    }
}
