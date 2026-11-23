<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Unit\Input;

use PHPUnit\Framework\TestCase;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Input\InvalidInputException;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Input\SiteSelection;

class SiteSelectionTest extends TestCase
{
    public function test_parse_all(): void
    {
        $selection = SiteSelection::parse(' ALL ');

        $this->assertTrue($selection->all);
        $this->assertSame([], $selection->ids);
    }

    /**
     * @dataProvider validLists
     */
    public function test_parse_lists($input, array $expected): void
    {
        $selection = SiteSelection::parse($input);

        $this->assertFalse($selection->all);
        $this->assertSame($expected, $selection->ids);
    }

    public static function validLists(): array
    {
        return [
            'int'             => [3, [3]],
            'numeric string'  => ['3', [3]],
            'comma list'      => ['2, 1,2', [2, 1]],
            'array'           => [['4', 5], [4, 5]],
            'trailing comma'  => ['1,', [1]],
            'spaces'          => [' 3 , 1 ', [3, 1]],
        ];
    }

    /**
     * @dataProvider invalidLists
     */
    public function test_parse_rejects($input, string $expectedKey): void
    {
        try {
            SiteSelection::parse($input);
            $this->fail('Expected InvalidInputException');
        } catch (InvalidInputException $e) {
            $this->assertSame($expectedKey, $e->translationKey);
        }
    }

    public static function invalidLists(): array
    {
        return [
            'empty string' => ['', 'FeinwerkDeleteVisitsFilter_ErrorNoSite'],
            'null'         => [null, 'FeinwerkDeleteVisitsFilter_ErrorNoSite'],
            'zero'         => ['0', 'FeinwerkDeleteVisitsFilter_ErrorInvalidSiteId'],
            'text'         => ['1,abc', 'FeinwerkDeleteVisitsFilter_ErrorInvalidSiteId'],
            'negative'     => [-2, 'FeinwerkDeleteVisitsFilter_ErrorInvalidSiteId'],
        ];
    }
}
