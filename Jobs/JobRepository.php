<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs;

interface JobRepository
{
    /** Columns a status transition may change besides the status. */
    public const TRANSITION_FIELDS = ['error', 'finished_at', 'max_visits', 'started_at'];

    /** Columns saveProgress() writes. */
    public const PROGRESS_FIELDS = ['estimated_visits', 'deleted_visits', 'deleted_rows', 'progress', 'messages', 'heartbeat_at'];

    /** Stores a new job and sets $job->idJob. */
    public function insert(Job $job): int;

    public function get(int $idJob): ?Job;

    /**
     * Claims a specific job: queued → running (started_at is set only the first time). Returns the job if it is
     * running afterwards (also when it already was), null otherwise.
     */
    public function claim(int $idJob, string $nowUtc): ?Job;

    /**
     * Resumes the oldest running job (an orphan – the caller holds the runner lock), otherwise claims the oldest
     * queued job.
     */
    public function claimNext(string $nowUtc): ?Job;

    /**
     * Atomically changes the status if the job is in one of $fromStatuses.
     *
     * @param string[] $fromStatuses
     * @param array<string, string|int|null> $fields extra columns, only TRANSITION_FIELDS
     */
    public function transition(int $idJob, array $fromStatuses, string $toStatus, array $fields = []): bool;

    /**
     * Writes counters and progress (always – work already done must be recorded) and returns whether the job is
     * still running.
     */
    public function saveProgress(Job $job, string $nowUtc): bool;

    /**
     * @return Job[] newest first
     */
    public function find(int $offset, int $limit, ?string $status = null, ?string $source = null): array;

    /**
     * @return Job[] queued, running and held jobs, oldest first
     */
    public function findActive(): array;

    public function anonymizeCreator(string $login): void;
}
