<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs;

use Piwik\Piwik;

class MatomoEventDispatcher implements EventDispatcher
{
    public function post(string $eventName, array $params): void
    {
        Piwik::postEvent($eventName, $params);
    }
}
