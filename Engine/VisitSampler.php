<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine;

use Matomo\Network\IPUtils;
use Piwik\Common;
use Piwik\Date;
use Piwik\Db;

class VisitSampler
{
    public function __construct(private VisitFinder $finder)
    {
    }

    /**
     * The first $limit matching visits with the details shown in the preview.
     *
     * @return list<array<string, mixed>>
     */
    public function sample(int $idSite, string $segment, DateWindow $window, string $timezone, int $limit): array
    {
        if ($limit < 1) {
            return [];
        }
        $ids = $this->finder->findIds($idSite, $segment, $window, $limit);
        if ($ids === []) {
            return [];
        }

        $sql = sprintf(
            'SELECT v.idvisit, v.idvisitor, v.visit_last_action_time, v.location_ip, v.referer_type, v.referer_name,'
            . ' v.config_browser_name, v.config_os, v.config_device_type, v.visit_total_actions, a.name AS entry_url'
            . ' FROM %s v LEFT JOIN %s a ON a.idaction = v.visit_entry_idaction_url'
            . ' WHERE v.idvisit IN (%s) ORDER BY v.visit_last_action_time ASC, v.idvisit ASC',
            Common::prefixTable('log_visit'),
            Common::prefixTable('log_action'),
            implode(',', array_map('intval', $ids))
        );

        return array_map(fn(array $row) => [
            'idsite' => $idSite,
            'idvisit' => (int) $row['idvisit'],
            'visitor_id' => bin2hex((string) $row['idvisitor']),
            'time' => Date::factory($row['visit_last_action_time'], $timezone)->toString('Y-m-d H:i:s'),
            'ip' => IPUtils::binaryToStringIP((string) $row['location_ip']),
            'referrer_type' => (int) $row['referer_type'],
            'referrer_name' => (string) $row['referer_name'],
            'browser' => (string) $row['config_browser_name'],
            'os' => (string) $row['config_os'],
            'device_type' => $row['config_device_type'] === null ? null : (int) $row['config_device_type'],
            'actions' => (int) $row['visit_total_actions'],
            'entry_url' => (string) $row['entry_url'],
        ], Db::fetchAll($sql));
    }
}
