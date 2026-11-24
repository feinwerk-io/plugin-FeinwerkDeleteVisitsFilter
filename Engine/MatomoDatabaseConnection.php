<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine;

use Piwik\Db;

/**
 * Matomo's connection. Its adapter forgets the credentials once connected (Adapter::factory() calls resetConfig()), so
 * it cannot reconnect by itself: drop the whole database object, and the next Db::get() creates and connects a new
 * one from the config. core:archive does the same every two hours (CronArchive::disconnectDb(), Matomo 5.0 to 6).
 */
final class MatomoDatabaseConnection implements DatabaseConnection
{
    public function reconnect(): void
    {
        try {
            Db::destroyDatabaseObject();
        } catch (\Throwable $e) {
            // closing a dead mysqli handle can fail before the object is dropped (PDO only drops it): drop it anyway
            Db::setDatabaseObject(null);
        }
    }
}
