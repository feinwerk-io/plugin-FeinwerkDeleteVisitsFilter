<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Fakes;

use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\SiteDirectory;

final class FakeSiteDirectory implements SiteDirectory
{
    /**
     * @param array<int, string> $timezones idsite => timezone
     */
    public function __construct(public array $timezones = [1 => 'UTC'])
    {
    }

    public function allIds(): array
    {
        $ids = array_keys($this->timezones);
        sort($ids);

        return $ids;
    }

    public function exists(int $idSite): bool
    {
        return isset($this->timezones[$idSite]);
    }

    public function timezone(int $idSite): string
    {
        return $this->timezones[$idSite];
    }

    public function name(int $idSite): string
    {
        return 'Site ' . $idSite;
    }
}
