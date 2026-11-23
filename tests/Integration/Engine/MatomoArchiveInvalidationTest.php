<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Integration\Engine;

use Piwik\Archive\ArchiveInvalidator;
use Piwik\Container\StaticContainer;
use Piwik\Piwik;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\MatomoArchiveInvalidation;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Fixtures\DeleteVisitsFixture;
use Piwik\Tests\Framework\TestCase\IntegrationTestCase;

/**
 * @group FeinwerkDeleteVisitsFilter
 */
class MatomoArchiveInvalidationTest extends IntegrationTestCase
{
    /** @var DeleteVisitsFixture */
    public static $fixture = null;

    public function test_invalidate_remembersEachLocalDayIncludingRollUpParents(): void
    {
        Piwik::addAction('Archiving.getIdSitesToMarkArchivesAsInvalidated', function (&$idSites) {
            $idSites[] = 1; // pretend website 1 is a roll-up containing website 2
        });
        $invalidator = StaticContainer::get(ArchiveInvalidator::class);

        (new MatomoArchiveInvalidation($invalidator))->invalidate(2, ['2026-02-01', '2026-02-02']);

        $remembered = $invalidator->getRememberedArchivedReportsThatShouldBeInvalidated();
        sort($remembered['2026-02-01']);
        $this->assertSame([1, 2], $remembered['2026-02-01']);
        $this->assertContains(2, $remembered['2026-02-02']);
    }
}

MatomoArchiveInvalidationTest::$fixture = new DeleteVisitsFixture();
