<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Unit\Activity;

use PHPUnit\Framework\TestCase;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Activity\JobObject;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Activity\JobObjectType;

/** The activity log's "Filter by item" names the deletion job of each entry (ActivityLog 6.1 getObject()). */
class JobObjectTest extends TestCase
{
    private function activity(): object
    {
        return new class {
            use JobObject;
        };
    }

    public function test_the_object_is_the_deletion_job(): void
    {
        $object = $this->activity()->getObject(['idjob' => '12', 'deleted' => 300200, 'segment' => 'visitIp==192.0.2.1']);

        self::assertSame(JobObjectType::TYPE, $object['type']);
        self::assertSame('12', $object['id']);
        self::assertNotSame('', (string) $object['name']);
        self::assertSame('', $object['context']);
        self::assertLessThanOrEqual(30, strlen(JobObjectType::TYPE));
        self::assertArrayHasKey(JobObjectType::TYPE, $this->activity()->getObjectTypeLabels());
    }

    public function test_no_object_without_a_job_id(): void
    {
        self::assertNull($this->activity()->getObject([]));
        self::assertNull($this->activity()->getObject(['idjob' => '']));
        self::assertNull($this->activity()->getObject(['idjob' => '0']));
        self::assertNull($this->activity()->getObject(['idjob' => 'abc']));
    }
}
