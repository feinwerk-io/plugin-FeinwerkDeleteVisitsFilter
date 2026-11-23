<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\Activity;


/**
 * The deletion job an activity entry is about, for the activity log's "Filter by item" (ActivityLog 6.1 and later; older
 * versions never call these methods). A trait, not a base class: ActivityLog instantiates every class it finds in
 * Activity/, and the index version constant must sit on the activity classes themselves (PHP 8.1 has no trait constants).
 * Pure functions of the stored parameters, as ActivityLog requires: they also run when old entries are indexed.
 */
trait JobObject
{
    /**
     * @param array $parameters the entry's parameters (extractParams(): idjob and more)
     * @return array{type: string, id: string, name: ?string, context: ?string}|null
     */
    public function getObject(array $parameters): ?array
    {
        $idJob = $parameters['idjob'] ?? null;
        if (!is_numeric($idJob) || (int) $idJob <= 0) {
            return null;
        }
        $id = (string) (int) $idJob;

        return ['type' => JobObjectType::TYPE, 'id' => $id, 'name' => JobObjectType::name($id), 'context' => ''];
    }

    /** @return array<string, string> */
    public function getObjectTypeLabels(): array
    {
        return [JobObjectType::TYPE => JobObjectType::label()];
    }
}
