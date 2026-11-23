<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\Input;

final class DateRange
{
    public const EARLIEST_DAY = '1992-01-01';

    private function __construct(
        public readonly string $from,
        public readonly string $to
    ) {
    }

    /**
     * @param mixed  $from     first local day, YYYY-MM-DD
     * @param mixed  $to       last local day, YYYY-MM-DD (inclusive)
     * @param string $todayUtc today's date in UTC; $to may be one day later (websites east of UTC)
     * @throws InvalidInputException
     */
    public static function fromInput($from, $to, string $todayUtc): self
    {
        $from = self::day($from);
        $to = self::day($to);

        if ($from < self::EARLIEST_DAY) {
            throw new InvalidInputException('FeinwerkDeleteVisitsFilter_ErrorDateTooEarly', [self::EARLIEST_DAY]);
        }
        if ($from > $to) {
            throw new InvalidInputException('FeinwerkDeleteVisitsFilter_ErrorDateOrder');
        }
        $latest = (new \DateTimeImmutable($todayUtc, new \DateTimeZone('UTC')))->modify('+1 day')->format('Y-m-d');
        if ($to > $latest) {
            throw new InvalidInputException('FeinwerkDeleteVisitsFilter_ErrorDateInFuture');
        }

        return new self($from, $to);
    }

    private static function day($value): string
    {
        $value = is_string($value) ? trim($value) : '';
        if (
            !preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $value, $m)
            || !checkdate((int) $m[2], (int) $m[3], (int) $m[1])
        ) {
            throw new InvalidInputException('FeinwerkDeleteVisitsFilter_ErrorInvalidDate', [$value]);
        }

        return $value;
    }
}
