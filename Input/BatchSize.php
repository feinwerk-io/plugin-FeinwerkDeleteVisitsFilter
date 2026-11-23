<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\Input;

final class BatchSize
{
    public const MIN = 100;
    public const MAX = 10000;
    public const DEFAULT = 1000;

    /**
     * Clamps to [MIN, MAX]; empty or non-numeric input yields $default.
     */
    public static function normalize($value, int $default = self::DEFAULT): int
    {
        if (is_string($value)) {
            $value = trim($value);
        }
        if ($value === null || $value === '' || !is_numeric($value)) {
            $value = $default;
        }

        return max(self::MIN, min(self::MAX, (int) $value));
    }
}
