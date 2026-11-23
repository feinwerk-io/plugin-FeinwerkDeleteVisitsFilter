<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine;

use Piwik\Common;
use Piwik\Plugins\SitesManager\Model as SitesModel;

class MatomoSiteDirectory implements SiteDirectory
{
    public function allIds(): array
    {
        $ids = array_map('intval', (new SitesModel())->getSitesId());
        sort($ids);

        return $ids;
    }

    public function exists(int $idSite): bool
    {
        // Not cached: a website may be deleted while a long job runs.
        return !empty((new SitesModel())->getSiteFromId($idSite));
    }

    public function timezone(int $idSite): string
    {
        return (string) $this->site($idSite)['timezone'];
    }

    public function name(int $idSite): string
    {
        // Stored HTML-encoded (as entered in the admin UI); callers escape for their own output.
        return Common::unsanitizeInputValue((string) $this->site($idSite)['name']);
    }

    /**
     * Read from the model on every call: no per-process cache (a long console run sees changes) and no
     * access check (the hourly task runs without a logged-in user).
     */
    private function site(int $idSite): array
    {
        $site = (new SitesModel())->getSiteFromId($idSite);
        if (empty($site)) {
            throw new \InvalidArgumentException("Website {$idSite} does not exist");
        }

        return $site;
    }
}
