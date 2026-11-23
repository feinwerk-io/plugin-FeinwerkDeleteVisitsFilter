<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\Activity;

use Piwik\Piwik;

/** The object type of deletion jobs in the activity log (own type: at most 30 characters, the house's fw prefix). */
final class JobObjectType
{
    public const TYPE = 'fw_dvf_deletion';

    /** "Deletion 12"; the bare number when translations are not available. */
    public static function name(string $id): string
    {
        try {
            return Piwik::translate('FeinwerkDeleteVisitsFilter_RunningJob', [$id]);
        } catch (\Throwable $e) {
            return '#' . $id;
        }
    }

    public static function label(): string
    {
        try {
            return Piwik::translate('FeinwerkDeleteVisitsFilter_ActivityObjectDeletions');
        } catch (\Throwable $e) {
            return 'Visit deletions';
        }
    }
}
