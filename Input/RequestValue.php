<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\Input;

final class RequestValue
{
    /**
     * Trimmed text of a scalar request value, '' for anything else. Matomo 6 no longer trims API parameters.
     */
    public static function text($value): string
    {
        return is_scalar($value) ? trim((string) $value) : '';
    }
}
