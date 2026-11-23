<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Unit\Engine;

use PHPUnit\Framework\TestCase;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\SegmentInspector;

class SegmentInspectorTest extends TestCase
{
    /**
     * @dataProvider queries
     */
    public function test_touchesActionTables(string $sql, bool $expected): void
    {
        $this->assertSame($expected, SegmentInspector::touchesActionTables($sql));
    }

    public static function queries(): array
    {
        return [
            'visit columns only' => [
                'SELECT log_visit.idvisit FROM matomo_log_visit AS log_visit WHERE log_visit.location_ip = ?',
                false,
            ],
            'entry page lookup is visit level' => [
                'SELECT log_visit.idvisit FROM matomo_log_visit AS log_visit WHERE log_visit.visit_entry_idaction_url IN (SELECT idaction FROM matomo_log_action WHERE name LIKE ?)',
                false,
            ],
            'action join' => [
                'SELECT log_inner.idvisit FROM (SELECT log_visit.idvisit FROM matomo_log_visit AS log_visit LEFT JOIN matomo_log_link_visit_action AS log_link_visit_action ON log_link_visit_action.idvisit = log_visit.idvisit) AS log_inner',
                true,
            ],
            'conversion join' => [
                'SELECT log_visit.idvisit FROM matomo_log_visit AS log_visit LEFT JOIN matomo_log_conversion AS log_conversion ON log_conversion.idvisit = log_visit.idvisit',
                true,
            ],
            'conversion item join, no prefix' => [
                'SELECT log_visit.idvisit FROM log_visit AS log_visit LEFT JOIN log_conversion_item AS log_conversion_item ON log_conversion_item.idvisit = log_visit.idvisit',
                true,
            ],
        ];
    }
}
