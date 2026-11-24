<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Unit\Jobs;

use PHPUnit\Framework\TestCase;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\DeletionPreview;

class DeletionPreviewQuotasTest extends TestCase
{
    /**
     * @dataProvider provideQuotas
     */
    public function test_sampleQuotas(array $counts, int $size, array $expected): void
    {
        $this->assertSame($expected, DeletionPreview::sampleQuotas($counts, $size));
    }

    public static function provideQuotas(): array
    {
        return [
            'evenly over the websites' => [[1 => 50000, 3 => 900, 7 => 40], 20, [1 => 7, 3 => 7, 7 => 6]],
            'a small website gives its rest to the others' => [[1 => 50000, 3 => 2], 20, [1 => 18, 3 => 2]],
            'websites without matches get none' => [[1 => 0, 2 => 5, 3 => 0], 20, [2 => 5]],
            'fewer matches than the sample' => [[1 => 2, 2 => 1], 10, [1 => 2, 2 => 1]],
            'no sample' => [[1 => 10], 0, []],
            'no matches' => [[1 => 0, 2 => 0], 20, []],
        ];
    }
}
