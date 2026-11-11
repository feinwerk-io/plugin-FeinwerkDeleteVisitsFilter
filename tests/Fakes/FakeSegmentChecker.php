<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Fakes;

use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\SegmentChecker;

final class FakeSegmentChecker implements SegmentChecker
{
    /** @var array<int, string> idsite => error message */
    public array $errors = [];

    public function check(string $segment, int $idSite): ?string
    {
        return $this->errors[$idSite] ?? null;
    }
}
