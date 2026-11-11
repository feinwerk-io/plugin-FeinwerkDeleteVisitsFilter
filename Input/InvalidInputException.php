<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\Input;

/**
 * Invalid user input. Carries a translation key so that callers (API, console) can show a localized message
 * while the validators stay free of Matomo's translator (and unit-testable).
 */
final class InvalidInputException extends \InvalidArgumentException
{
    /**
     * @param list<string|int> $params
     */
    public function __construct(
        public readonly string $translationKey,
        public readonly array $params = []
    ) {
        parent::__construct($translationKey . ($params === [] ? '' : ' [' . implode(', ', $params) . ']'));
    }
}
