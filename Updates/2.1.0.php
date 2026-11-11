<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter;

use Piwik\Container\StaticContainer;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\JobStore;
use Piwik\Updater;
use Piwik\Updates as PiwikUpdates;

/**
 * Creates the job table on installations updated from 2.0.x (new installations get it from install()).
 */
class Updates_2_1_0 extends PiwikUpdates
{
    public function doUpdate(Updater $updater)
    {
        StaticContainer::get(JobStore::class)->install();
    }
}
