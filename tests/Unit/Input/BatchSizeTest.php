<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Unit\Input;

use PHPUnit\Framework\TestCase;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Input\BatchSize;

class BatchSizeTest extends TestCase
{
    /**
     * @dataProvider values
     */
    public function test_normalize($input, int $default, int $expected): void
    {
        $this->assertSame($expected, BatchSize::normalize($input, $default));
    }

    public static function values(): array
    {
        return [
            'string number' => ['2500', 1000, 2500],
            'spaces'        => [' 2500 ', 1000, 2500],
            'clamped low'   => ['50', 1000, 100],
            'clamped high'  => [50000, 1000, 10000],
            'empty'         => ['', 500, 500],
            'null'          => [null, 1000, 1000],
            'garbage'       => ['abc', 1000, 1000],
        ];
    }
}
