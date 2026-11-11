<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Fakes;

use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\EventDispatcher;

final class RecordingEventDispatcher implements EventDispatcher
{
    /** @var list<array{0: string, 1: array}> */
    public array $events = [];

    public function post(string $eventName, array $params): void
    {
        $this->events[] = [$eventName, $params];
    }

    /** @return string[] */
    public function names(): array
    {
        return array_map(fn(array $event) => $event[0], $this->events);
    }
}
