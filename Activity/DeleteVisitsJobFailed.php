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

class DeleteVisitsJobFailed extends Activity
{
    use JobObject;

    /** Bump when getObject() changes, so ActivityLog indexes the existing entries again. */
    public const OBJECT_INDEX_VERSION = 1;

    protected $eventName = 'FeinwerkDeleteVisitsFilter.jobFailed';

    public function getPerformingUser($eventData = null)
    {
        // Jobs may finish in the background (task, CLI): name the person who created the job.
        $creator = $eventData[0]['created_by'] ?? null;

        return is_string($creator) && $creator !== '' ? $creator : parent::getPerformingUser($eventData);
    }

    public function extractParams($eventData)
    {
        [$job] = $eventData;

        return ['idjob' => $job['idjob'], 'error' => (string) $job['error']];
    }

    public function getTranslatedDescription($activityData, $performingUser)
    {
        return Piwik::translate('FeinwerkDeleteVisitsFilter_ActivityJobFailed', [$activityData['idjob'], $activityData['error']]);
    }
}
