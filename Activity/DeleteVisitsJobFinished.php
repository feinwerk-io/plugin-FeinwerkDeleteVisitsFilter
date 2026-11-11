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

/**
 * Loaded only by the ActivityLog plugin (component discovery); without it this class is never touched.
 */
class DeleteVisitsJobFinished extends Activity
{
    protected $eventName = 'FeinwerkDeleteVisitsFilter.jobFinished';

    public function getPerformingUser($eventData = null)
    {
        // Jobs may finish in the background (task, CLI): name the person who created the job.
        $creator = $eventData[0]['created_by'] ?? null;

        return is_string($creator) && $creator !== '' ? $creator : parent::getPerformingUser($eventData);
    }

    public function extractParams($eventData)
    {
        [$job] = $eventData;

        return [
            'idjob' => $job['idjob'],
            'deleted' => $job['deleted_visits'],
            'segment' => $job['segment'],
            'sites' => implode(', ', $job['idsites']),
            'days' => Piwik::translate('FeinwerkDeleteVisitsFilter_DateRangeValue', [$job['date_from'], $job['date_to']]),
        ];
    }

    public function getTranslatedDescription($activityData, $performingUser)
    {
        return Piwik::translate('FeinwerkDeleteVisitsFilter_ActivityJobFinished', [
            $activityData['deleted'],
            $activityData['segment'],
            $activityData['sites'],
            $activityData['days'],
        ]);
    }
}
