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
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Input\SegmentDefinition;

class SegmentDefinitionTest extends TestCase
{
    public function test_fromInput_trims(): void
    {
        $this->assertSame('visitIp==1.2.3.4', SegmentDefinition::fromInput("  visitIp==1.2.3.4\n"));
    }

    public function test_fromInput_rejectsEmpty(): void
    {
        $this->expectException(InvalidInputException::class);
        $this->expectExceptionMessage('FeinwerkDeleteVisitsFilter_ErrorNoSegment');

        SegmentDefinition::fromInput('   ');
    }
}
