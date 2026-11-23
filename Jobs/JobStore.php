<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs;

use Piwik\Common;
use Piwik\Db;
use Piwik\DbHelper;

class JobStore implements JobRepository
{
    public const TABLE = 'fw_deletevisitsfilter_job';

    public static function tableName(): string
    {
        return Common::prefixTable(self::TABLE);
    }

    public function install(): void
    {
        // UNIQUE (idrule, run_key) already prepares idempotent rule runs (Etappe 3); NULLs never collide.
        DbHelper::createTable(self::TABLE, "
            idjob INT UNSIGNED NOT NULL AUTO_INCREMENT,
            status VARCHAR(20) NOT NULL,
            source VARCHAR(20) NOT NULL,
            idrule INT UNSIGNED NULL,
            run_key VARCHAR(40) NULL,
            idsites LONGTEXT NOT NULL,
            segment TEXT NOT NULL,
            date_from DATE NOT NULL,
            date_to DATE NOT NULL,
            batch_size INT UNSIGNED NOT NULL,
            max_visits INT UNSIGNED NULL,
            estimated_visits BIGINT UNSIGNED NULL,
            deleted_visits BIGINT UNSIGNED NOT NULL DEFAULT 0,
            deleted_rows LONGTEXT NULL,
            progress LONGTEXT NULL,
            messages LONGTEXT NULL,
            error TEXT NULL,
            created_by VARCHAR(100) NULL,
            created_at DATETIME NOT NULL,
            started_at DATETIME NULL,
            finished_at DATETIME NULL,
            heartbeat_at DATETIME NULL,
            PRIMARY KEY (idjob),
            INDEX index_status (status, idjob),
            UNIQUE KEY unique_rule_run (idrule, run_key)
        ");
    }

    public function uninstall(): void
    {
        Db::query('DROP TABLE IF EXISTS `' . self::tableName() . '`');
    }

    public function insert(Job $job): int
    {
        $row = $job->toRow();
        $sql = sprintf(
            'INSERT INTO `%s` (%s) VALUES (%s)',
            self::tableName(),
            implode(', ', array_keys($row)),
            implode(', ', array_fill(0, count($row), '?'))
        );
        Db::query($sql, array_values($row));
        $job->idJob = (int) Db::get()->lastInsertId();

        return $job->idJob;
    }

    public function get(int $idJob): ?Job
    {
        $row = Db::fetchRow('SELECT * FROM `' . self::tableName() . '` WHERE idjob = ?', [$idJob]);

        return $row ? Job::fromRow($row) : null;
    }

    public function claim(int $idJob, string $nowUtc): ?Job
    {
        Db::query(
            'UPDATE `' . self::tableName() . '` SET status = ?, started_at = COALESCE(started_at, ?), heartbeat_at = ?'
            . ' WHERE idjob = ? AND status = ?',
            [Job::STATUS_RUNNING, $nowUtc, $nowUtc, $idJob, Job::STATUS_QUEUED]
        );
        $job = $this->get($idJob);

        return $job !== null && $job->status === Job::STATUS_RUNNING ? $job : null;
    }

    public function claimNext(string $nowUtc): ?Job
    {
        $running = Db::fetchOne(
            'SELECT idjob FROM `' . self::tableName() . '` WHERE status = ? ORDER BY idjob ASC LIMIT 1',
            [Job::STATUS_RUNNING]
        );
        if ($running) {
            return $this->get((int) $running);
        }
        $queued = Db::fetchOne(
            'SELECT idjob FROM `' . self::tableName() . '` WHERE status = ? ORDER BY idjob ASC LIMIT 1',
            [Job::STATUS_QUEUED]
        );

        return $queued ? $this->claim((int) $queued, $nowUtc) : null;
    }

    public function transition(int $idJob, array $fromStatuses, string $toStatus, array $fields = []): bool
    {
        if (in_array($toStatus, $fromStatuses, true)) {
            throw new \InvalidArgumentException('The target status must differ from the source statuses');
        }
        $set = ['status = ?'];
        $bind = [$toStatus];
        foreach ($fields as $column => $value) {
            if (!in_array($column, JobRepository::TRANSITION_FIELDS, true)) {
                throw new \InvalidArgumentException("Column {$column} cannot be changed by a transition");
            }
            $set[] = $column . ' = ?';
            $bind[] = $value;
        }
        $bind[] = $idJob;
        array_push($bind, ...$fromStatuses);
        $sql = 'UPDATE `' . self::tableName() . '` SET ' . implode(', ', $set)
            . ' WHERE idjob = ? AND status IN (' . implode(', ', array_fill(0, count($fromStatuses), '?')) . ')';

        // The status always changes, so the affected-row count is reliable here.
        return Db::query($sql, $bind)->rowCount() > 0;
    }

    public function saveProgress(Job $job, string $nowUtc): bool
    {
        $job->heartbeatAt = $nowUtc;
        $row = $job->toRow();
        $set = [];
        $bind = [];
        foreach (JobRepository::PROGRESS_FIELDS as $column) {
            $set[] = $column . ' = ?';
            $bind[] = $row[$column];
        }
        $bind[] = $job->idJob;
        Db::query('UPDATE `' . self::tableName() . '` SET ' . implode(', ', $set) . ' WHERE idjob = ?', $bind);

        // Read the status instead of trusting rowCount(): MySQL reports 0 rows for unchanged data.
        return Db::fetchOne('SELECT status FROM `' . self::tableName() . '` WHERE idjob = ?', [$job->idJob]) === Job::STATUS_RUNNING;
    }

    public function find(int $offset, int $limit, ?string $status = null, ?string $source = null): array
    {
        $where = [];
        $bind = [];
        if ($status !== null && $status !== '') {
            $where[] = 'status = ?';
            $bind[] = $status;
        }
        if ($source !== null && $source !== '') {
            $where[] = 'source = ?';
            $bind[] = $source;
        }
        $sql = 'SELECT * FROM `' . self::tableName() . '`'
            . ($where === [] ? '' : ' WHERE ' . implode(' AND ', $where))
            . ' ORDER BY idjob DESC LIMIT ' . max(0, $offset) . ', ' . max(1, $limit);

        return array_map([Job::class, 'fromRow'], Db::fetchAll($sql, $bind));
    }

    public function findActive(): array
    {
        $statuses = Job::ACTIVE_STATUSES;
        $sql = 'SELECT * FROM `' . self::tableName() . '` WHERE status IN ('
            . implode(', ', array_fill(0, count($statuses), '?')) . ') ORDER BY idjob ASC';

        return array_map([Job::class, 'fromRow'], Db::fetchAll($sql, $statuses));
    }

    public function anonymizeCreator(string $login): void
    {
        Db::query('UPDATE `' . self::tableName() . '` SET created_by = NULL WHERE created_by = ?', [$login]);
    }
}
