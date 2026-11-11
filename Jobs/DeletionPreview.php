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
        $sample = [];
        foreach ($input['idSites'] as $idSite) {
            $timezone = $this->sites->timezone($idSite);
            $window = DateWindow::forLocalDays($input['range']->from, $input['range']->to, $timezone);
            $count = $this->finder->count($idSite, $input['segment'], $window);
            $total += $count;
            $sites[] = ['idsite' => $idSite, 'name' => $this->sites->name($idSite), 'count' => $count];
            $actionLevel = $actionLevel || $this->finder->isActionLevel($idSite, $input['segment'], $window);
            if ($count > 0 && count($sample) < $sampleSize) {
                $sample = array_merge($sample, $this->sampler->sample($idSite, $input['segment'], $window, $timezone, $sampleSize - count($sample)));
            }
        }

        return ['total' => $total, 'action_level' => $actionLevel, 'sites' => $sites, 'sample' => $sample];
    }
}
