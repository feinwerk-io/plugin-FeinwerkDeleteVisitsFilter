<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine;

/**
 * A database error worth retrying: deadlock (1213) or lock wait timeout (1205).
 */
final class TransientDatabaseException extends \RuntimeException
{
    /**
     * Runs $query; deadlocks and lock wait timeouts are rethrown as TransientDatabaseException.
     *
     * @return mixed the result of $query
     */
    public static function wrap(callable $query)
    {
        try {
            return $query();
        } catch (TransientDatabaseException $e) {
            throw $e;
        } catch (\Exception $e) {
            if (self::isTransient($e)) {
                throw new self($e->getMessage(), (int) $e->getCode(), $e);
            }
            throw $e;
        }
    }

    public static function isTransient(\Throwable $e): bool
    {
        // PDO: the MySQL error number appears in the message (same detection as Matomo's isErrNo).
        // MYSQLI: the message has no number, the error number is the (int) exception code.
        return in_array($e->getCode(), [1205, 1213], true)
            || (bool) preg_match('/(?:\[|\s)(1205|1213)(?:\]|\s)/', $e->getMessage());
    }
}
