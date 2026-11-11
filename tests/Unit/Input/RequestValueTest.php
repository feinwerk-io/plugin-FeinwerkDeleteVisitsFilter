<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Unit\Input;

use PHPUnit\Framework\TestCase;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Input\RequestValue;

class RequestValueTest extends TestCase
{
    /**
     * @dataProvider values
     */
    public function test_text($input, string $expected): void
    {
        $this->assertSame($expected, RequestValue::text($input));
    }

    public static function values(): array
    {
        return [
            'plain'           => ['done', 'done'],
            'spaces'          => ['  done ', 'done'],
            'tab and newline' => ["\tui\n", 'ui'],
            'int'             => [5, '5'],
            'null'            => [null, ''],
            'array'           => [['done'], ''],
        ];
    }
}
