<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Fakes;

use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\ArchiveInvalidation;

final class FakeInvalidation implements ArchiveInvalidation
{
    /** @var list<array{0: int, 1: string[]}> */
    public array $calls = [];

    public function __construct(private CallLog $log)
    {
    }

    public function invalidate(int $idSite, array $localDays): void
    {
        $this->calls[] = [$idSite, $localDays];
        $this->log->entries[] = 'invalidate:' . $idSite . ':' . implode(',', $localDays);
    }
}
