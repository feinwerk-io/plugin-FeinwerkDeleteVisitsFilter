<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine;

use Piwik\Date;

/**
 * Half-open UTC interval [fromUtc, toUtcExclusive) covering whole local calendar days of a website.
 */
final class DateWindow
{
    public function __construct(
        public readonly string $fromUtc,
        public readonly string $toUtcExclusive
    ) {
    }

    /**
     * @param string $fromDay  first local day, YYYY-MM-DD (inclusive)
     * @param string $toDay    last local day, YYYY-MM-DD (inclusive)
     * @param string $timezone site timezone as stored by Matomo, e.g. "Europe/Berlin" or "UTC+5.5"
     */
    public static function forLocalDays(string $fromDay, string $toDay, string $timezone): self
    {
        // Matomo's Date treats the timestamp as local time of $timezone; getTimestamp() converts it to UTC.
        $start = Date::factory($fromDay)->setTimezone($timezone)->getTimestamp();
        $end = Date::factory($toDay)->addDay(1)->setTimezone($timezone)->getTimestamp();

        return new self(gmdate('Y-m-d H:i:s', $start), gmdate('Y-m-d H:i:s', $end));
    }

    /**
     * Local calendar day (YYYY-MM-DD) of a UTC datetime in the given timezone.
     */
    public static function localDayOf(string $utcDatetime, string $timezone): string
    {
        return Date::factory($utcDatetime, $timezone)->toString('Y-m-d');
    }
}
