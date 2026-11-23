<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs;

use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Clock;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\SegmentChecker;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\SiteDirectory;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Input\BatchSize;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Input\DateRange;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Input\InvalidInputException;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Input\SegmentDefinition;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Input\SiteSelection;

/**
 * Validates raw input (API, console) and creates queued jobs. Shared by every entry point.
 */
final class JobFactory
{
    public function __construct(
        private SiteDirectory $sites,
        private SegmentChecker $segments,
        private JobRepository $jobs,
        private Clock $clock
    ) {
    }

    /**
     * @return array{idSites: int[], segment: string, range: DateRange}
     * @throws InvalidInputException
     */
    public function validate($idSites, $segment, $dateFrom, $dateTo): array
    {
        $segment = SegmentDefinition::fromInput($segment);
        $range = DateRange::fromInput($dateFrom, $dateTo, gmdate('Y-m-d', $this->clock->now()));
        $ids = $this->resolveSites(SiteSelection::parse($idSites));

        $errors = [];
        foreach ($ids as $idSite) {
            $error = $this->segments->check($segment, $idSite);
            if ($error !== null) {
                $errors[] = sprintf('%s (%d): %s', $this->sites->name($idSite), $idSite, $error);
            }
        }
        if ($errors !== []) {
            throw new InvalidInputException('FeinwerkDeleteVisitsFilter_ErrorSegmentInvalidForSites', [implode('; ', $errors)]);
        }

        return ['idSites' => $ids, 'segment' => $segment, 'range' => $range];
    }

    /**
     * @throws InvalidInputException
     */
    public function create($idSites, $segment, $dateFrom, $dateTo, $batchSize, int $defaultBatchSize, string $source, ?string $createdBy): Job
    {
        $input = $this->validate($idSites, $segment, $dateFrom, $dateTo);
        $job = Job::create(
            $source,
            $input['idSites'],
            $input['segment'],
            $input['range']->from,
            $input['range']->to,
            BatchSize::normalize($batchSize, $defaultBatchSize),
            $createdBy,
            gmdate('Y-m-d H:i:s', $this->clock->now())
        );
        $this->jobs->insert($job);

        return $job;
    }

    /**
     * @return int[]
     */
    private function resolveSites(SiteSelection $selection): array
    {
        if ($selection->all) {
            $ids = $this->sites->allIds();
            if ($ids === []) {
                throw new InvalidInputException('FeinwerkDeleteVisitsFilter_ErrorNoSite');
            }

            return $ids;
        }
        $unknown = array_values(array_filter($selection->ids, fn(int $idSite) => !$this->sites->exists($idSite)));
        if ($unknown !== []) {
            throw new InvalidInputException('FeinwerkDeleteVisitsFilter_ErrorUnknownSites', [implode(', ', $unknown)]);
        }

        return $selection->ids;
    }
}
