<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Unit;

use PHPUnit\Framework\TestCase;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\DateWindow;

class AutoloadTest extends TestCase
{
    public function test_pluginClassesLoadFromThisCheckout(): void
    {
        $file = (new \ReflectionClass(DateWindow::class))->getFileName();

        $this->assertStringStartsWith(dirname(__DIR__, 2) . DIRECTORY_SEPARATOR, $file);
    }
}
