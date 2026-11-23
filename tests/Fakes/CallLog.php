<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Fakes;

/** Shared, ordered record of calls across several fakes (to assert ordering). */
final class CallLog
{
    /** @var string[] */
    public array $entries = [];
}
