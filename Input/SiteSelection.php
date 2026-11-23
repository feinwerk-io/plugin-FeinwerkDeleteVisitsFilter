<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\Input;

final class SiteSelection
{
    /**
     * @param int[] $ids
     */
    private function __construct(
        public readonly bool $all,
        public readonly array $ids
    ) {
    }

    /**
     * Accepts "all", an int, a comma-separated string or an array of ids. Keeps the order, drops duplicates.
     *
     * @throws InvalidInputException
     */
    public static function parse($value): self
    {
        if (is_string($value) && strtolower(trim($value)) === 'all') {
            return new self(true, []);
        }
        if (is_int($value)) {
            $value = [$value];
        } elseif (is_string($value)) {
            $value = explode(',', $value);
        }
        if (!is_array($value)) {
            throw new InvalidInputException('FeinwerkDeleteVisitsFilter_ErrorNoSite');
        }

        $ids = [];
        foreach ($value as $part) {
            if (is_string($part)) {
                $part = trim($part);
            }
            if ($part === '' || $part === null) {
                continue;
            }
            if (is_int($part)) {
                $id = $part;
            } elseif (is_string($part) && ctype_digit($part)) {
                $id = (int) $part;
            } else {
                throw new InvalidInputException('FeinwerkDeleteVisitsFilter_ErrorInvalidSiteId', [is_scalar($part) ? (string) $part : gettype($part)]);
            }
            if ($id < 1) {
                throw new InvalidInputException('FeinwerkDeleteVisitsFilter_ErrorInvalidSiteId', [(string) $id]);
            }
            $ids[$id] = $id;
        }

        if ($ids === []) {
            throw new InvalidInputException('FeinwerkDeleteVisitsFilter_ErrorNoSite');
        }

        return new self(false, array_values($ids));
    }
}
