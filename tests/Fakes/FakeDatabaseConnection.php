<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Fakes;

use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\DatabaseConnection;

final class FakeDatabaseConnection implements DatabaseConnection
{
    public int $reconnects = 0;

    public function reconnect(): void
    {
        $this->reconnects++;
    }
}
