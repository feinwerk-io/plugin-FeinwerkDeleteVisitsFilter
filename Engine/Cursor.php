<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine;

/**
 * Position in the (visit_last_action_time, idvisit) order of a website's visits.
 */
final class Cursor
{
    public function __construct(
        public readonly string $time,
        public readonly int $idVisit
    ) {
    }

    /**
     * @return array{0: string, 1: int}
     */
    public function toArray(): array
    {
        return [$this->time, $this->idVisit];
    }

    public static function fromArray(array $data): self
    {
        return new self((string) $data[0], (int) $data[1]);
    }
}
