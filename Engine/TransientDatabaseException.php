<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine;

/**
 * A database error worth retrying: deadlock (1213), lock wait timeout (1205), or a lost connection, which needs a
 * reconnect first: 2006 "MySQL server has gone away", 2013 "Lost connection to MySQL server during query", 4031 (MySQL
 * 8: disconnected because of inactivity), and 2002/2003 "Can't connect" while the server restarts.
 */
final class TransientDatabaseException extends \RuntimeException
{
    /**
     * Runs $query; deadlocks, lock wait timeouts and lost connections are rethrown as TransientDatabaseException.
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
        return self::hasErrorNumber($e, [1205, 1213]) || self::isConnectionLost($e);
    }

    private const CONNECTION_ERRORS = [2002, 2003, 2006, 2013, 4031];
    private const CONNECTION_MESSAGES = [
        'server has gone away',
        'Lost connection to MySQL server',
        'disconnected by the server because of inactivity',
        "Can't connect to MySQL server",
        "Can't connect to local MySQL server",
        'Connection refused',
    ];

    /**
     * The connection is gone or cannot be opened right now (2002, 2003, 2006, 2013, 4031), here or in a previous
     * exception of the chain.
     */
    public static function isConnectionLost(\Throwable $e): bool
    {
        for ($current = $e; $current !== null; $current = $current->getPrevious()) {
            if (self::hasErrorNumber($current, self::CONNECTION_ERRORS)) {
                return true;
            }
            foreach (self::CONNECTION_MESSAGES as $message) {
                if (stripos($current->getMessage(), $message) !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    /** @param int[] $numbers */
    private static function hasErrorNumber(\Throwable $e, array $numbers): bool
    {
        // PDO: the MySQL error number appears in the message (same detection as Matomo's isErrNo).
        // MYSQLI: the message has no number, the error number is the (int) exception code.
        return in_array($e->getCode(), $numbers, true)
            || (bool) preg_match('/(?:\[|\s)(' . implode('|', $numbers) . ')(?:\]|\s)/', $e->getMessage());
    }
}
