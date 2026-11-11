<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Unit\Engine;

use PHPUnit\Framework\TestCase;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\DateWindow;

class DateWindowTest extends TestCase
{
    /**
     * @dataProvider windows
     */
    public function test_forLocalDays_convertsLocalMidnightsToUtc(string $from, string $to, string $tz, string $expFrom, string $expTo): void
    {
        $window = DateWindow::forLocalDays($from, $to, $tz);

        $this->assertSame($expFrom, $window->fromUtc);
        $this->assertSame($expTo, $window->toUtcExclusive);
    }

    public static function windows(): array
    {
        return [
            'utc single day'   => ['2026-10-06', '2026-10-06', 'UTC', '2026-10-06 00:00:00', '2026-10-07 00:00:00'],
            'berlin summer'    => ['2026-10-06', '2026-10-06', 'Europe/Berlin', '2026-10-05 22:00:00', '2026-10-06 22:00:00'],
            'berlin dst start' => ['2026-03-29', '2026-03-29', 'Europe/Berlin', '2026-03-28 23:00:00', '2026-03-29 22:00:00'],
            'berlin dst end'   => ['2026-10-25', '2026-10-25', 'Europe/Berlin', '2026-10-24 22:00:00', '2026-10-25 23:00:00'],
            'new york month'   => ['2026-10-01', '2026-10-31', 'America/New_York', '2026-10-01 04:00:00', '2026-11-01 04:00:00'],
            'manual offset'    => ['2026-10-06', '2026-10-06', 'UTC+5.5', '2026-10-05 18:30:00', '2026-10-06 18:30:00'],
        ];
    }

    public function test_localDayOf_usesTheSiteTimezone(): void
    {
        $this->assertSame('2026-10-06', DateWindow::localDayOf('2026-10-05 22:30:00', 'Europe/Berlin'));
        $this->assertSame('2026-10-05', DateWindow::localDayOf('2026-10-05 21:59:59', 'Europe/Berlin'));
        $this->assertSame('2026-10-05', DateWindow::localDayOf('2026-10-06 03:59:59', 'America/New_York'));
        $this->assertSame('2026-10-06', DateWindow::localDayOf('2026-10-06 00:00:00', 'UTC'));
    }
}
