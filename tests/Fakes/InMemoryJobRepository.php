<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Fakes;

use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\Job;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\JobRepository;

/**
 * Stores rows (not objects) so that every read goes through Job::fromRow() like the real table.
 */
final class InMemoryJobRepository implements JobRepository
{
    /** @var array<int, array<string, mixed>> */
    private array $rows = [];
    private int $nextId = 1;

    public function insert(Job $job): int
    {
        $id = $this->nextId++;
        $this->rows[$id] = ['idjob' => $id] + $job->toRow();
        $job->idJob = $id;

        return $id;
    }

    public function get(int $idJob): ?Job
    {
        return isset($this->rows[$idJob]) ? Job::fromRow($this->rows[$idJob]) : null;
    }

    public function claim(int $idJob, string $nowUtc): ?Job
    {
        if (!isset($this->rows[$idJob])) {
            return null;
        }
        if ($this->rows[$idJob]['status'] === Job::STATUS_QUEUED) {
            $this->rows[$idJob]['status'] = Job::STATUS_RUNNING;
            $this->rows[$idJob]['started_at'] = $this->rows[$idJob]['started_at'] ?? $nowUtc;
            $this->rows[$idJob]['heartbeat_at'] = $nowUtc;
        }

        return $this->rows[$idJob]['status'] === Job::STATUS_RUNNING ? $this->get($idJob) : null;
    }

    public function claimNext(string $nowUtc): ?Job
    {
        foreach ($this->rows as $id => $row) {
            if ($row['status'] === Job::STATUS_RUNNING) {
                return $this->get($id);
            }
        }
        foreach ($this->rows as $id => $row) {
            if ($row['status'] === Job::STATUS_QUEUED) {
                return $this->claim($id, $nowUtc);
            }
        }

        return null;
    }

    public function transition(int $idJob, array $fromStatuses, string $toStatus, array $fields = []): bool
    {
        if (in_array($toStatus, $fromStatuses, true)) {
            throw new \InvalidArgumentException('The target status must differ from the source statuses');
        }
        foreach (array_keys($fields) as $column) {
            if (!in_array($column, JobRepository::TRANSITION_FIELDS, true)) {
                throw new \InvalidArgumentException("Column {$column} cannot be changed by a transition");
            }
        }
        if (!isset($this->rows[$idJob]) || !in_array($this->rows[$idJob]['status'], $fromStatuses, true)) {
            return false;
        }
        $this->rows[$idJob] = array_merge($this->rows[$idJob], $fields, ['status' => $toStatus]);

        return true;
    }

    public function saveProgress(Job $job, string $nowUtc): bool
    {
        if (!isset($this->rows[$job->idJob])) {
            return false;
        }
        $job->heartbeatAt = $nowUtc;
        $row = $job->toRow();
        foreach (JobRepository::PROGRESS_FIELDS as $column) {
            $this->rows[$job->idJob][$column] = $row[$column];
        }

        return $this->rows[$job->idJob]['status'] === Job::STATUS_RUNNING;
    }

    public function find(int $offset, int $limit, ?string $status = null, ?string $source = null): array
    {
        $rows = array_filter($this->rows, fn(array $row) =>
            ($status === null || $status === '' || $row['status'] === $status)
            && ($source === null || $source === '' || $row['source'] === $source));
        krsort($rows);

        return array_map([Job::class, 'fromRow'], array_values(array_slice($rows, max(0, $offset), max(1, $limit))));
    }

    public function findActive(): array
    {
        $rows = array_filter($this->rows, fn(array $row) => in_array($row['status'], Job::ACTIVE_STATUSES, true));
        ksort($rows);

        return array_map([Job::class, 'fromRow'], array_values($rows));
    }

    public function anonymizeCreator(string $login): void
    {
        foreach ($this->rows as $id => $row) {
            if ($row['created_by'] === $login) {
                $this->rows[$id]['created_by'] = null;
            }
        }
    }
}
