<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Unit\Engine;

use PHPUnit\Framework\TestCase;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\TransientDatabaseException;

class TransientDatabaseExceptionTest extends TestCase
{
    public function test_wrap_returnsTheResult(): void
    {
        $this->assertSame(42, TransientDatabaseException::wrap(fn() => 42));
    }

    /**
     * @dataProvider transientMessages
     */
    public function test_wrap_marksDeadlocksAndLockWaitTimeouts(string $message): void
    {
        try {
            TransientDatabaseException::wrap(function () use ($message) {
                throw new \Exception($message);
            });
            $this->fail('Expected TransientDatabaseException');
        } catch (TransientDatabaseException $e) {
            $this->assertSame($message, $e->getMessage());
            $this->assertInstanceOf(\Exception::class, $e->getPrevious());
        }
    }

    public static function transientMessages(): array
    {
        return [
            'deadlock'  => ['SQLSTATE[40001]: Serialization failure: 1213 Deadlock found when trying to get lock; try restarting transaction'],
            'lock wait' => ['SQLSTATE[HY000]: General error: 1205 Lock wait timeout exceeded; try restarting transaction'],
        ];
    }

    /**
     * The MYSQLI adapter reports the MySQL error number only as exception code, not in the message.
     *
     * @dataProvider mysqliStyleExceptions
     */
    public function test_isTransient_recognisesMysqliErrorCodes(string $message, int $code, bool $expected): void
    {
        $this->assertSame($expected, TransientDatabaseException::isTransient(new \Exception($message, $code)));
    }

    public static function mysqliStyleExceptions(): array
    {
        return [
            'deadlock'     => ['Mysqli statement execute error : Deadlock found when trying to get lock; try restarting transaction', 1213, true],
            'lock wait'    => ['Mysqli statement execute error : Lock wait timeout exceeded; try restarting transaction', 1205, true],
            'missing table' => ["Mysqli statement execute error : Table 'x' doesn't exist", 1146, false],
        ];
    }

    public function test_wrap_rethrowsOtherErrorsUnchanged(): void
    {
        $original = new \RuntimeException('SQLSTATE[42S02]: Base table or view not found: 1146 Table x does not exist');

        try {
            TransientDatabaseException::wrap(function () use ($original) {
                throw $original;
            });
            $this->fail('Expected exception');
        } catch (\Throwable $e) {
            $this->assertSame($original, $e);
        }
    }
}
