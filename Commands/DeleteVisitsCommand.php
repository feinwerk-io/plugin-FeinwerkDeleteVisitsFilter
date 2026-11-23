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
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\DeletionPreview;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\Job;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\JobFactory;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\JobRepository;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\JobRunner;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\SystemSettings;

class DeleteVisitsCommand extends ConsoleCommand
{
    protected function configure(): void
    {
        $this->setName('feinwerkdeletevisitsfilter:delete');
        $this->setAliases(['deletevisitsfilter:delete']);
        $this->setDescription('Delete visits matching a segment filter in bulk');
        $this->addRequiredArgument('idsite', 'Website ID, comma-separated website IDs, or "all"');
        $this->addRequiredArgument('segment', 'Segment filter (e.g. visitIp==192.168.1.1)');
        $this->addRequiredArgument('date-from', 'First day (YYYY-MM-DD, in the website\'s timezone)');
        $this->addRequiredArgument('date-to', 'Last day (YYYY-MM-DD, inclusive)');
        $this->addOptionalArgument('batch-size', 'Visits per batch (100-10000)', '');
        $this->addNoValueOption('dry-run', null, 'Only show the matching visits, delete nothing');
        $this->addNoValueOption('yes', 'y', 'Do not ask for confirmation');
    }

    protected function doExecute(): int
    {
        Piwik::checkUserHasSuperUserAccess();
        $input = $this->getInput();
        $output = $this->getOutput();
        $args = [$input->getArgument('idsite'), $input->getArgument('segment'), $input->getArgument('date-from'), $input->getArgument('date-to')];

        try {
            $summary = StaticContainer::get(DeletionPreview::class)->build(...[...$args, 0]);
        } catch (InvalidInputException $e) {
            $output->writeln('<error>' . Piwik::translate($e->translationKey, $e->params) . '</error>');

            return self::FAILURE;
        }

        $this->renderTable(['Website', 'Matching visits'], array_map(
            fn(array $site) => [sprintf('%s (%d)', $site['name'], $site['idsite']), number_format($site['count'])],
            $summary['sites']
        ));
        $output->writeln(sprintf('<info>Total: %s matching visits.</info>', number_format($summary['total'])));
        if ($summary['action_level']) {
            $output->writeln('<comment>The segment uses page or conversion conditions: the WHOLE matching visits will be deleted.</comment>');
        }
        if ($input->getOption('dry-run')) {
            return self::SUCCESS;
        }
        if ($summary['total'] === 0) {
            $output->writeln('<comment>Nothing to delete.</comment>');

            return self::SUCCESS;
        }
        $question = sprintf('Delete %s visits? This cannot be undone. [y/N] ', number_format($summary['total']));
        if (!$input->getOption('yes') && !$input->isInteractive()) {
            $output->writeln('<error>Add --yes to delete without confirmation (non-interactive run).</error>');

            return self::FAILURE;
        }
        if (!$input->getOption('yes') && !$this->askForConfirmation($question, false)) {
            $output->writeln('<comment>Aborted.</comment>');

            return self::SUCCESS;
        }

        $job = StaticContainer::get(JobFactory::class)->create(
            ...[...$args, $input->getArgument('batch-size'), (int) (new SystemSettings())->defaultBatchSize->getValue(), Job::SOURCE_CONSOLE, null]
        );
        $output->writeln(sprintf('<info>Job #%d created, deleting…</info>', $job->idJob));
        $output->writeln(sprintf(
            'Interrupting this command does not cancel the deletion; it continues in the background. To cancel: ./console feinwerkdeletevisitsfilter:jobs --cancel=%d',
            $job->idJob
        ));

        $result = StaticContainer::get(JobRunner::class)->run(null, $job->idJob, function (Job $progress) use ($output) {
            $output->writeln(sprintf('  %s / ~%s visits deleted', number_format($progress->deletedVisits), number_format((int) $progress->estimatedVisits)));
        });
        if ($result->busy) {
            $output->writeln(sprintf(
                '<comment>Another deletion is running. Job #%d is queued and will be processed by the hourly background task or by ./console feinwerkdeletevisitsfilter:work.</comment>',
                $job->idJob
            ));

            return self::SUCCESS;
        }

        $job = StaticContainer::get(JobRepository::class)->get($job->idJob);
        JobsCommand::printJob($output, $job);

        return $job->status === Job::STATUS_DONE ? self::SUCCESS : self::FAILURE;
    }
}
