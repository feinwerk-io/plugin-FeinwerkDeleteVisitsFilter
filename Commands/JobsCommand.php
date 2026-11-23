<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\Commands;

use Piwik\Container\StaticContainer;
use Piwik\Piwik;
use Piwik\Plugin\ConsoleCommand;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Input\InvalidInputException;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\Job;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\JobControl;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\JobRepository;
use Symfony\Component\Console\Output\OutputInterface;

class JobsCommand extends ConsoleCommand
{
    protected function configure(): void
    {
        $this->setName('feinwerkdeletevisitsfilter:jobs');
        $this->setDescription('List visit deletion jobs or cancel one');
        $this->addRequiredValueOption('limit', null, 'Number of jobs to show', 20);
        $this->addRequiredValueOption('cancel', null, 'Cancel the job with this ID');
    }

    protected function doExecute(): int
    {
        $output = $this->getOutput();
        $cancel = $this->getInput()->getOption('cancel');
        if ($cancel !== null && $cancel !== '') {
            try {
                self::printJob($output, StaticContainer::get(JobControl::class)->cancel((int) $cancel));
            } catch (InvalidInputException $e) {
                $output->writeln('<error>' . Piwik::translate($e->translationKey, $e->params) . '</error>');

                return self::FAILURE;
            }

            return self::SUCCESS;
        }

        $jobs = StaticContainer::get(JobRepository::class)->find(0, max(1, (int) $this->getInput()->getOption('limit')));
        $this->renderTable(
            ['ID', 'Status', 'Source', 'Websites', 'Segment', 'Days', 'Deleted', 'Created (UTC)', 'Finished (UTC)'],
            array_map(fn(Job $job) => [
                $job->idJob,
                $job->status,
                $job->source,
                implode(',', $job->idSites),
                $job->segment,
                Piwik::translate('FeinwerkDeleteVisitsFilter_DateRangeValue', [$job->dateFrom, $job->dateTo]),
                number_format($job->deletedVisits),
                $job->createdAt,
                (string) $job->finishedAt,
            ], $jobs)
        );

        return self::SUCCESS;
    }

    public static function printJob(OutputInterface $output, Job $job): void
    {
        $output->writeln(sprintf('<info>Job #%d %s: %s visits deleted.</info>', $job->idJob, $job->status, number_format($job->deletedVisits)));
        foreach ($job->deletedRows as $table => $rows) {
            $output->writeln(sprintf('  %-40s %s', $table, number_format($rows)));
        }
        $output->writeln(sprintf('  Days marked for re-processing: %d', $job->progress->invalidatedCount()));
        if ($job->error !== null) {
            $output->writeln('<error>' . $job->error . '</error>');
        }
    }
}
