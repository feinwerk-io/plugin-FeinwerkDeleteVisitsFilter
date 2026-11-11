<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs;

final class Job
{
    public const STATUS_QUEUED = 'queued';
    public const STATUS_RUNNING = 'running';
    public const STATUS_HELD = 'held';
    public const STATUS_DONE = 'done';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_FAILED = 'failed';
    public const ACTIVE_STATUSES = [self::STATUS_QUEUED, self::STATUS_RUNNING, self::STATUS_HELD];

    public const SOURCE_UI = 'ui';
    public const SOURCE_API = 'api';
    public const SOURCE_CONSOLE = 'console';
    public const SOURCE_RULE = 'rule';

    public ?int $idJob = null;
    public string $status = self::STATUS_QUEUED;
    public string $source = self::SOURCE_API;
    public ?int $idRule = null;
    public ?string $runKey = null;
    /** @var int[] */
    public array $idSites = [];
    public string $segment = '';
    public string $dateFrom = '';
    public string $dateTo = '';
    public int $batchSize = 1000;
    public ?int $maxVisits = null;
    public ?int $estimatedVisits = null;
    public int $deletedVisits = 0;
    /** @var array<string, int> */
    public array $deletedRows = [];
    public JobProgress $progress;
    /** @var list<array{key: string, params: list<string|int>}> */
    public array $messages = [];
    public ?string $error = null;
    public ?string $createdBy = null;
    public string $createdAt = '';
    public ?string $startedAt = null;
    public ?string $finishedAt = null;
    public ?string $heartbeatAt = null;

    private function __construct()
    {
        $this->progress = new JobProgress();
    }

    /**
     * @param int[] $idSites
     */
    public static function create(
        string $source,
        array $idSites,
        string $segment,
        string $dateFrom,
        string $dateTo,
        int $batchSize,
        ?string $createdBy,
        string $createdAt
    ): self {
        $job = new self();
        $job->source = $source;
        $job->idSites = array_values(array_map('intval', $idSites));
        $job->segment = $segment;
        $job->dateFrom = $dateFrom;
        $job->dateTo = $dateTo;
        $job->batchSize = $batchSize;
        $job->createdBy = $createdBy;
        $job->createdAt = $createdAt;

        return $job;
    }

    /**
     * @param array<string, int|string> $rows table => deleted rows
     */
    public function addDeletedRows(array $rows): void
    {
        foreach ($rows as $table => $count) {
            $this->deletedRows[(string) $table] = ($this->deletedRows[(string) $table] ?? 0) + (int) $count;
        }
    }

    /**
     * @param list<string|int> $params
     */
    public function addMessage(string $key, array $params = []): void
    {
        $this->messages[] = ['key' => $key, 'params' => $params];
    }

    public static function fromRow(array $row): self
    {
        $job = new self();
        $job->idJob = isset($row['idjob']) ? (int) $row['idjob'] : null;
        $job->status = (string) $row['status'];
        $job->source = (string) $row['source'];
        $job->idRule = self::intOrNull($row['idrule'] ?? null);
        $job->runKey = isset($row['run_key']) ? (string) $row['run_key'] : null;
        $job->idSites = array_values(array_map('intval', json_decode((string) $row['idsites'], true) ?: []));
        $job->segment = (string) $row['segment'];
        $job->dateFrom = (string) $row['date_from'];
        $job->dateTo = (string) $row['date_to'];
        $job->batchSize = (int) $row['batch_size'];
        $job->maxVisits = self::intOrNull($row['max_visits'] ?? null);
        $job->estimatedVisits = self::intOrNull($row['estimated_visits'] ?? null);
        $job->deletedVisits = (int) ($row['deleted_visits'] ?? 0);
        $job->deletedRows = array_map('intval', json_decode((string) ($row['deleted_rows'] ?? ''), true) ?: []);
        $job->progress = JobProgress::fromArray(json_decode((string) ($row['progress'] ?? ''), true) ?: []);
        $job->messages = json_decode((string) ($row['messages'] ?? ''), true) ?: [];
        $job->error = isset($row['error']) ? (string) $row['error'] : null;
        $job->createdBy = isset($row['created_by']) ? (string) $row['created_by'] : null;
        $job->createdAt = (string) $row['created_at'];
        $job->startedAt = isset($row['started_at']) ? (string) $row['started_at'] : null;
        $job->finishedAt = isset($row['finished_at']) ? (string) $row['finished_at'] : null;
        $job->heartbeatAt = isset($row['heartbeat_at']) ? (string) $row['heartbeat_at'] : null;

        return $job;
    }

    /**
     * @return array<string, string|int|null> column => value, without idjob
     */
    public function toRow(): array
    {
        return [
            'status' => $this->status,
            'source' => $this->source,
            'idrule' => $this->idRule,
            'run_key' => $this->runKey,
            'idsites' => json_encode($this->idSites),
            'segment' => $this->segment,
            'date_from' => $this->dateFrom,
            'date_to' => $this->dateTo,
            'batch_size' => $this->batchSize,
            'max_visits' => $this->maxVisits,
            'estimated_visits' => $this->estimatedVisits,
            'deleted_visits' => $this->deletedVisits,
            'deleted_rows' => json_encode((object) $this->deletedRows),
            'progress' => json_encode($this->progress->toArray()),
            'messages' => json_encode($this->messages),
            'error' => $this->error,
            'created_by' => $this->createdBy,
            'created_at' => $this->createdAt,
            'started_at' => $this->startedAt,
            'finished_at' => $this->finishedAt,
            'heartbeat_at' => $this->heartbeatAt,
        ];
    }

    public function toApiArray(): array
    {
        return [
            'idjob' => $this->idJob,
            'status' => $this->status,
            'source' => $this->source,
            'idrule' => $this->idRule,
            'idsites' => $this->idSites,
            'segment' => $this->segment,
            'date_from' => $this->dateFrom,
            'date_to' => $this->dateTo,
            'batch_size' => $this->batchSize,
            'max_visits' => $this->maxVisits,
            'estimated_visits' => $this->estimatedVisits,
            'deleted_visits' => $this->deletedVisits,
            'deleted_rows' => $this->deletedRows,
            'batches' => $this->progress->batches,
            'invalidated_dates' => $this->progress->invalidatedCount(),
            'site_index' => $this->progress->siteIndex,
            'sites_total' => count($this->idSites),
            'messages' => $this->messages,
            'error' => $this->error,
            'created_by' => $this->createdBy,
            'created_at' => $this->createdAt,
            'started_at' => $this->startedAt,
            'finished_at' => $this->finishedAt,
            'heartbeat_at' => $this->heartbeatAt,
        ];
    }

    private static function intOrNull($value): ?int
    {
        return $value === null || $value === '' ? null : (int) $value;
    }
}
