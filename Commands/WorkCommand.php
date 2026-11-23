<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\Commands;

use Piwik\Container\StaticContainer;
use Piwik\Plugin\ConsoleCommand;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\Job;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\JobRunner;

class WorkCommand extends ConsoleCommand
{
    protected function configure(): void
    {
        $this->setName('feinwerkdeletevisitsfilter:work');
        $this->setDescription('Process queued visit deletion jobs (for cron or very large deletions)');
        $this->addRequiredValueOption('max-time', null, 'Stop after this many seconds (default: no limit)');
    }

    protected function doExecute(): int
    {
        $output = $this->getOutput();
        $maxTime = $this->getInput()->getOption('max-time');
        $budget = ($maxTime === null || $maxTime === '') ? null : max(1, (int) $maxTime);

        $result = StaticContainer::get(JobRunner::class)->run($budget, null, function (Job $job) use ($output) {
            $output->writeln(sprintf('  Job #%d: %s / ~%s visits deleted', $job->idJob, number_format($job->deletedVisits), number_format((int) $job->estimatedVisits)));
        });
        if ($result->busy) {
            $output->writeln('<comment>Another process is already working on the deletion queue.</comment>');

            return self::SUCCESS;
        }
        $output->writeln(sprintf('<info>Processed %d job(s).</info>', count($result->processedJobIds)));

        return self::SUCCESS;
    }
}
