<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine;

interface SiteDirectory
{
    /** @return int[] all website ids, ascending */
    public function allIds(): array;

    public function exists(int $idSite): bool;

    public function timezone(int $idSite): string;

    public function name(int $idSite): string;
}
