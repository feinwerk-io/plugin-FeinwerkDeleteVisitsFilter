<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine;

use Piwik\Archive\ArchiveInvalidator;
use Piwik\Date;
use Piwik\Piwik;

class MatomoArchiveInvalidation implements ArchiveInvalidation
{
    public function __construct(private ArchiveInvalidator $invalidator)
    {
    }

    public function invalidate(int $idSite, array $localDays): void
    {
        if ($localDays === []) {
            return;
        }
        $idSites = [$idSite];
        // Same event as the GDPR tool: lets RollUpReporting add the roll-ups containing this website.
        Piwik::postEvent('Archiving.getIdSitesToMarkArchivesAsInvalidated', [&$idSites, $localDays, null, null, null, true]);

        foreach (array_unique(array_map('intval', $idSites)) as $site) {
            foreach ($localDays as $day) {
                $this->invalidator->rememberToInvalidateArchivedReportsLater($site, Date::factory($day));
            }
        }
    }
}
