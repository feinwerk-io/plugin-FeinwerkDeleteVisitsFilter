<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter;

use Piwik\Access;
use Piwik\Container\StaticContainer;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\JobRunner;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\MatomoRunnerLock;

class Tasks extends \Piwik\Plugin\Tasks
{
    public function schedule()
    {
        // The scheduler's own task lock must outlive our budget plus the runner lock's TTL.
        $this->hourly('processQueue', null, self::LOWEST_PRIORITY, $this->budget() + MatomoRunnerLock::TTL);
    }

    /**
     * Continues deletions in the background, e.g. after the admin page was closed.
     */
    public function processQueue(): void
    {
        $budget = $this->budget();
        if ($budget <= 0) {
            return;
        }
        // Never lower an unlimited limit (CLI) or an already larger one.
        $limit = (int) ini_get('max_execution_time');
        if ($limit !== 0 && $limit < $budget + MatomoRunnerLock::TTL) {
            @set_time_limit($budget + MatomoRunnerLock::TTL);
        }
        // The runner calls Matomo core (segment SQL, archive invalidation, data subject deletion), which check access;
        // not every task trigger guarantees a super user. Site lookups no longer depend on it.
        Access::doAsSuperUser(function () use ($budget) {
            StaticContainer::get(JobRunner::class)->run($budget);
        });
    }

    private function budget(): int
    {
        return (int) (new SystemSettings())->backgroundTimeBudget->getValue();
    }
}
