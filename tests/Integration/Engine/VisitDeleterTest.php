<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Integration\Engine;

use Piwik\Common;
use Piwik\Container\StaticContainer;
use Piwik\Db;
use Piwik\Piwik;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\DateWindow;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\VisitDeleter;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\VisitFinder;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Fixtures\DeleteVisitsFixture;
use Piwik\Tests\Framework\TestCase\IntegrationTestCase;

/**
 * @group FeinwerkDeleteVisitsFilter
 */
class VisitDeleterTest extends IntegrationTestCase
{
    /** @var DeleteVisitsFixture */
    public static $fixture = null;

    private VisitDeleter $deleter;

    public function setUp(): void
    {
        parent::setUp();
        $this->deleter = StaticContainer::get(VisitDeleter::class);
    }

    private function rows(string $table, ?array $idVisits = null): int
    {
        $sql = 'SELECT COUNT(*) FROM ' . Common::prefixTable($table);
        if ($idVisits !== null) {
            $sql .= ' WHERE idvisit IN (' . implode(',', array_map('intval', $idVisits)) . ')';
        }

        return (int) Db::fetchOne($sql);
    }

    public function test_purge_deletesTheVisitsFromAllLogTablesAndNothingElse(): void
    {
        $bots = (new VisitFinder())->matches(1, 'visitIp==' . DeleteVisitsFixture::BOT_IP, DateWindow::forLocalDays('2026-03-14', '2026-03-14', 'UTC'), null, null);
        $ids = array_column($bots, 'idvisit');
        $visitsBefore = $this->rows('log_visit');
        $actionsBefore = $this->rows('log_link_visit_action');
        $conversionsBefore = $this->rows('log_conversion');

        $result = $this->deleter->purge(1, $bots);

        $this->assertSame(2, $result->deletedVisits);
        $this->assertSame(3, $result->rowsPerTable['log_link_visit_action']);
        $this->assertSame(0, $this->rows('log_visit', $ids));
        $this->assertSame(0, $this->rows('log_link_visit_action', $ids));
        $this->assertSame($visitsBefore - 2, $this->rows('log_visit'));
        $this->assertSame($actionsBefore - 3, $this->rows('log_link_visit_action'));
        $this->assertSame($conversionsBefore, $this->rows('log_conversion'));
    }

    public function test_purge_deletesConversionsOfThatVisitOnly(): void
    {
        $human = DeleteVisitsFixture::visitIdAt(1, '2026-03-14 10:01:00');
        $otherOrders = (int) Db::fetchOne('SELECT COUNT(*) FROM ' . Common::prefixTable('log_conversion') . ' WHERE idsite = 2');
        $this->assertGreaterThan(0, $this->rows('log_conversion', [$human]));

        $this->deleter->purge(1, [['idvisit' => $human, 'time' => '2026-03-14 10:01:00']]);

        $this->assertSame(0, $this->rows('log_conversion', [$human]));
        $this->assertSame(0, $this->rows('log_conversion_item', [$human]));
        $this->assertSame($otherOrders, (int) Db::fetchOne('SELECT COUNT(*) FROM ' . Common::prefixTable('log_conversion') . ' WHERE idsite = 2'));
    }

    public function test_purge_letsPluginsDeleteTheirOwnData(): void
    {
        $id = DeleteVisitsFixture::visitIdAt(1, '2026-03-14 09:00:00');
        $captured = null;
        Piwik::addAction('PrivacyManager.deleteDataSubjects', function (&$results, $visits) use (&$captured) {
            $captured = $visits;
            $results['myplugin'] = 7;
        });

        $result = $this->deleter->purge(1, [['idvisit' => $id, 'time' => '2026-03-14 09:00:00']]);

        $this->assertSame([['idsite' => 1, 'idvisit' => $id]], $captured);
        $this->assertSame(7, $result->rowsPerTable['myplugin']);
    }
}

VisitDeleterTest::$fixture = new DeleteVisitsFixture();
