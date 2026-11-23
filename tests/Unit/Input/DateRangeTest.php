<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Unit\Input;

use PHPUnit\Framework\TestCase;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Input\DateRange;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Input\InvalidInputException;

class DateRangeTest extends TestCase
{
    public function test_fromInput_acceptsAValidRange(): void
    {
        $range = DateRange::fromInput('2026-03-01', '2026-03-14', '2026-10-06');

        $this->assertSame('2026-03-01', $range->from);
        $this->assertSame('2026-03-14', $range->to);
    }

    public function test_fromInput_trimsSurroundingSpaces(): void
    {
        // Why: Matomo 6 no longer trims API parameters.
        $range = DateRange::fromInput(' 2026-03-01 ', "2026-03-14\n", '2026-10-06');

        $this->assertSame('2026-03-01', $range->from);
        $this->assertSame('2026-03-14', $range->to);
    }

    public function test_fromInput_allowsTomorrowForSitesAheadOfUtc(): void
    {
        $this->assertSame('2026-10-07', DateRange::fromInput('2026-10-01', '2026-10-07', '2026-10-06')->to);
    }

    /**
     * @dataProvider invalidRanges
     */
    public function test_fromInput_rejectsInvalidInput($from, $to, string $expectedKey): void
    {
        try {
            DateRange::fromInput($from, $to, '2026-10-06');
            $this->fail('Expected InvalidInputException');
        } catch (InvalidInputException $e) {
            $this->assertSame($expectedKey, $e->translationKey);
        }
    }

    public static function invalidRanges(): array
    {
        return [
            'wrong format'   => ['2026-3-1', '2026-03-14', 'FeinwerkDeleteVisitsFilter_ErrorInvalidDate'],
            'impossible day' => ['2026-02-30', '2026-03-14', 'FeinwerkDeleteVisitsFilter_ErrorInvalidDate'],
            'empty'          => ['', '2026-03-14', 'FeinwerkDeleteVisitsFilter_ErrorInvalidDate'],
            'not a string'   => [null, '2026-03-14', 'FeinwerkDeleteVisitsFilter_ErrorInvalidDate'],
            'reversed'       => ['2026-03-14', '2026-03-01', 'FeinwerkDeleteVisitsFilter_ErrorDateOrder'],
            'too early'      => ['1991-12-31', '2026-03-01', 'FeinwerkDeleteVisitsFilter_ErrorDateTooEarly'],
            'future'         => ['2026-10-01', '2026-10-08', 'FeinwerkDeleteVisitsFilter_ErrorDateInFuture'],
        ];
    }
}
