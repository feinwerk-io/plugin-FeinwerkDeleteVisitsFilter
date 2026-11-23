<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine;

use Piwik\Piwik;
use Piwik\Plugins\PrivacyManager\Model\DataSubjects;

/**
 * Deletes visits exactly like Matomo's GDPR tool and core LogDeleter: the core routine knows every registered log
 * table (including those of other plugins), orders them and deletes log_visit last. log_action is never touched.
 */
class VisitDeleter implements VisitPurger
{
    public function __construct(private DataSubjects $dataSubjects)
    {
    }

    public function purge(int $idSite, array $visits): PurgeResult
    {
        if ($visits === []) {
            return new PurgeResult(0, []);
        }

        $results = [];
        $subjects = array_map(fn(array $visit) => ['idsite' => $idSite, 'idvisit' => (int) $visit['idvisit']], $visits);
        // Plugins that keep visit data outside the log tables delete it on this event (same as the GDPR tool).
        Piwik::postEvent('PrivacyManager.deleteDataSubjects', [&$results, $subjects]);

        // idvisit is unique across websites; without idsite the core builds a plain "idvisit IN (…)".
        $counts = TransientDatabaseException::wrap(fn() => $this->dataSubjects->deleteDataSubjectsWithoutInvalidatingArchives(
            array_map(fn(array $visit) => ['idvisit' => (int) $visit['idvisit']], $visits)
        ));

        $rows = [];
        foreach (array_merge($results, $counts) as $table => $count) {
            if (is_numeric($count)) {
                $rows[(string) $table] = (int) $count;
            }
        }

        return new PurgeResult((int) ($counts['log_visit'] ?? 0), $rows);
    }
}
