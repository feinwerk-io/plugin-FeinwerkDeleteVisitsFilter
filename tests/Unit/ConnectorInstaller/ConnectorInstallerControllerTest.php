<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Unit\ConnectorInstaller;

use PHPUnit\Framework\TestCase;

/**
 * The installer page is an admin page (found in a release review, 2026-11-19): with the general (report) variables it
 * broke without idSite (HTTP 500 when the menu entry is opened from a page without a website) and followed
 * enable_framed_pages instead of Matomo's admin rule enable_framed_settings. The page itself needs Matomo's request
 * environment, so the sandbox check (CLAUDE.md "Connector installer") opens it without idSite; this test pins the
 * controller's choices.
 */
class ConnectorInstallerControllerTest extends TestCase
{
    private function method(string $name): string
    {
        $source = (string) file_get_contents(__DIR__ . '/../../../Controller.php');
        self::assertSame(1, preg_match('/function ' . $name . '\(.*?\n    \}\n/s', $source, $match), $name);

        return $match[0];
    }

    public function test_the_page_sets_the_admin_variables_and_needs_no_website(): void
    {
        $render = $this->method('renderConnectorInstaller');

        self::assertStringContainsString('$this->setBasicVariablesView($view);', $render);
        self::assertStringNotContainsString('setGeneralVariablesView', $render);
        // ControllerAdmin's setBasicVariablesView() sets the admin variables already; a second call repeats Matomo's
        // admin notifications
        self::assertTrue(is_subclass_of(\Piwik\Plugins\FeinwerkDeleteVisitsFilter\Controller::class, \Piwik\Plugin\ControllerAdmin::class));
        self::assertStringNotContainsString('setBasicVariablesAdminView($view)', $render);
    }

    public function test_the_click_stops_without_a_recent_password_check_and_leads_out_when_hidden(): void
    {
        $run = $this->method('connectorInstallerRun');

        self::assertSame(1, preg_match('/\$verified = .*?requirePasswordVerifiedRecently\(/s', $run));
        $stop = strpos($run, 'if (!$verified) {');
        self::assertNotFalse($stop, 'the click checks the password result');
        self::assertSame(1, preg_match('/^if \(!\$verified\) \{\s*(\/\/[^\n]*\s*)?return \'\';/', substr($run, (int) $stop)));
        self::assertLessThan(
            strpos($run, 'requirePasswordVerifiedRecently'),
            strpos($run, 'ConnectorInstaller::redirectFor($env)'),
            'nothing to offer: out (connector or admin home) before the password check'
        );
        self::assertStringContainsString('ConnectorInstaller::redirectFor($env)', $this->method('connectorInstaller'));
    }
}
