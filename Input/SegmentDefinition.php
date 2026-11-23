<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\Input;

final class SegmentDefinition
{
    /**
     * @throws InvalidInputException
     */
    public static function fromInput($value): string
    {
        $value = is_string($value) ? trim($value) : '';
        if ($value === '') {
            throw new InvalidInputException('FeinwerkDeleteVisitsFilter_ErrorNoSegment');
        }

        return $value;
    }
}
