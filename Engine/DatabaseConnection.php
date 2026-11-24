<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine;

/**
 * The database connection of this request, as far as the job runner needs it: after "MySQL server has gone away" (a
 * short wait_timeout, a database restart) it drops the dead connection so the next query opens a new one.
 */
interface DatabaseConnection
{
    public function reconnect(): void;
}
