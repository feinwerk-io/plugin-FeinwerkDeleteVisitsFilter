<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Fakes;

use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\PurgeResult;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\TransientDatabaseException;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\VisitPurger;

final class FakePurger implements VisitPurger
{
    /** @var list<array{idsite: int, ids: int[]}> */
    public array $calls = [];

    /** Seconds the clock advances per successful purge. */
    public int $secondsPerPurge = 0;

    /** Number of calls that fail with a deadlock (or $transientMessage) before purging works. */
    public int $transientFailures = 0;
    public string $transientMessage = 'SQLSTATE[40001]: Serialization failure: 1213 Deadlock found when trying to get lock';
    /** @var list<string> messages of transient failures thrown one per call before $transientFailures */
    public array $transientMessages = [];
    /** Throw them as the driver does (a plain exception), not wrapped in TransientDatabaseException. */
    public bool $rawTransient = false;

    /** Thrown AFTER deleting on call number $failOnCall (1-based). */
    public ?\Throwable $failWith = null;
    public int $failOnCall = 0;

    /** @var list<callable(int): void> called with the call number after each purge */
    public array $afterPurge = [];

    public function __construct(
        private FakeVisitSource $source,
        private FakeClock $clock,
        private CallLog $log
    ) {
    }

    public function purge(int $idSite, array $visits): PurgeResult
    {
        if ($this->transientMessages !== []) {
            $message = array_shift($this->transientMessages);
            throw $this->rawTransient ? new \RuntimeException($message) : new TransientDatabaseException($message);
        }
        if ($this->transientFailures > 0) {
            $this->transientFailures--;
            throw new TransientDatabaseException($this->transientMessage);
        }
        $ids = array_map(fn(array $visit) => $visit['idvisit'], $visits);
        foreach ($ids as $id) {
            $this->source->remove($id);
        }
        $this->calls[] = ['idsite' => $idSite, 'ids' => $ids];
        $this->log->entries[] = 'purge:' . $idSite . ':' . implode(',', $ids);
        $this->clock->advance($this->secondsPerPurge);

        $call = count($this->calls);
        foreach ($this->afterPurge as $hook) {
            $hook($call);
        }
        if ($this->failWith !== null && $call === $this->failOnCall) {
            throw $this->failWith;
        }

        return new PurgeResult(count($ids), ['log_visit' => count($ids), 'log_link_visit_action' => 2 * count($ids)]);
    }

    /** @return int[] in purge order */
    public function purgedIds(): array
    {
        return array_merge([], ...array_column($this->calls, 'ids'));
    }
}
