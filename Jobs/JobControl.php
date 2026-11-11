<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs;

use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Clock;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Input\InvalidInputException;

final class JobControl
{
    public const EVENT_CANCELLED = 'FeinwerkDeleteVisitsFilter.jobCancelled';

    public function __construct(
        private JobRepository $jobs,
        private EventDispatcher $events,
        private Clock $clock
    ) {
    }

    /**
     * Cancels a queued, running or held job. A running job stops before its next batch.
     *
     * @throws InvalidInputException
     */
    public function cancel(int $idJob): Job
    {
        $job = $this->load($idJob);
        $cancelled = $this->jobs->transition($idJob, Job::ACTIVE_STATUSES, Job::STATUS_CANCELLED, [
            'finished_at' => gmdate('Y-m-d H:i:s', $this->clock->now()),
        ]);
        if (!$cancelled) {
            throw new InvalidInputException('FeinwerkDeleteVisitsFilter_ErrorJobStateChange', [$idJob, $job->status]);
        }
        $job = $this->load($idJob);
        $this->events->post(self::EVENT_CANCELLED, [$job->toApiArray()]);

        return $job;
    }

    /**
     * Re-queues a failed job; it continues where it stopped.
     *
     * @throws InvalidInputException
     */
    public function retry(int $idJob): Job
    {
        $job = $this->load($idJob);
        if (!$this->jobs->transition($idJob, [Job::STATUS_FAILED], Job::STATUS_QUEUED, ['error' => null, 'finished_at' => null])) {
            throw new InvalidInputException('FeinwerkDeleteVisitsFilter_ErrorJobStateChange', [$idJob, $job->status]);
        }

        return $this->load($idJob);
    }

    private function load(int $idJob): Job
    {
        $job = $this->jobs->get($idJob);
        if ($job === null) {
            throw new InvalidInputException('FeinwerkDeleteVisitsFilter_ErrorJobNotFound', [$idJob]);
        }

        return $job;
    }
}
