<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs;

use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\DateWindow;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\SiteDirectory;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\VisitFinder;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\VisitSampler;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Input\InvalidInputException;

class DeletionPreview
{
    public function __construct(
        private JobFactory $factory,
        private VisitFinder $finder,
        private VisitSampler $sampler,
        private SiteDirectory $sites
    ) {
    }

    /**
     * @return array{total: int, action_level: bool, sites: list<array{idsite: int, name: string, count: int}>, sample: list<array>}
     * @throws InvalidInputException
     */
    public function build($idSites, $segment, $dateFrom, $dateTo, int $sampleSize): array
    {
        $input = $this->factory->validate($idSites, $segment, $dateFrom, $dateTo);
        $total = 0;
        $actionLevel = false;
        $sites = [];
        $windows = [];
        $counts = [];
        foreach ($input['idSites'] as $idSite) {
            $timezone = $this->sites->timezone($idSite);
            $window = DateWindow::forLocalDays($input['range']->from, $input['range']->to, $timezone);
            $windows[$idSite] = [$window, $timezone];
            $count = $this->finder->count($idSite, $input['segment'], $window);
            $counts[$idSite] = $count;
            $total += $count;
            $sites[] = ['idsite' => $idSite, 'name' => $this->sites->name($idSite), 'count' => $count];
            $actionLevel = $actionLevel || $this->finder->isActionLevel($idSite, $input['segment'], $window);
        }
        $sample = [];
        foreach (self::sampleQuotas($counts, $sampleSize) as $idSite => $quota) {
            [$window, $timezone] = $windows[$idSite];
            $sample = array_merge($sample, $this->sampler->sample($idSite, $input['segment'], $window, $timezone, $quota));
        }

        return ['total' => $total, 'action_level' => $actionLevel, 'sites' => $sites, 'sample' => $sample];
    }

    /**
     * Spreads the sample over the websites with matches, one row at a time in turn, so a website with few matches is
     * not hidden behind the first one.
     *
     * @param array<int, int> $counts matches per website ID, in the order of the request
     * @return array<int, int> rows per website ID (only websites with at least one row), same order
     */
    public static function sampleQuotas(array $counts, int $size): array
    {
        $quotas = array_fill_keys(array_keys($counts), 0);
        $left = max(0, $size);
        while ($left > 0) {
            $added = false;
            foreach ($counts as $idSite => $count) {
                if ($left > 0 && $quotas[$idSite] < $count) {
                    $quotas[$idSite]++;
                    $left--;
                    $added = true;
                }
            }
            if (!$added) {
                break;
            }
        }

        return array_filter($quotas, fn(int $quota) => $quota > 0);
    }
}
