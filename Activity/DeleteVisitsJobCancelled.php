<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\Activity;

use Piwik\Piwik;
use Piwik\Plugins\ActivityLog\Activity\Activity;

class DeleteVisitsJobCancelled extends Activity
{
    protected $eventName = 'FeinwerkDeleteVisitsFilter.jobCancelled';

    public function getPerformingUser($eventData = null)
    {
        // Jobs may finish in the background (task, CLI): name the person who created the job.
        $creator = $eventData[0]['created_by'] ?? null;

        return is_string($creator) && $creator !== '' ? $creator : parent::getPerformingUser($eventData);
    }

    public function extractParams($eventData)
    {
        [$job] = $eventData;

        return ['idjob' => $job['idjob'], 'deleted' => $job['deleted_visits']];
    }

    public function getTranslatedDescription($activityData, $performingUser)
    {
        return Piwik::translate('FeinwerkDeleteVisitsFilter_ActivityJobCancelled', [$activityData['idjob'], $activityData['deleted']]);
    }
}
