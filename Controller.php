<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter;

use Piwik\Common;
use Piwik\Container\StaticContainer;
use Piwik\Nonce;
use Piwik\Piwik;
use Piwik\Plugin\Manager as PluginManager;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\ConnectorInstaller\ConnectorInstaller;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Promo\PromoLine;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Promo\PromoPlacements;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Ui\AdminPageProps;
use Piwik\Url;
use Piwik\View;

class Controller extends \Piwik\Plugin\ControllerAdmin
{
    public function index(): string
    {
        Piwik::checkUserHasSuperUserAccess();

        $view = new View('@FeinwerkDeleteVisitsFilter/index');
        $this->setGeneralVariablesView($view);
        $idSite = Common::getRequestVar('idSite', 0, 'int');
        $view->props = StaticContainer::get(AdminPageProps::class)->build($idSite);
        $view->promoLine = self::promoLine($idSite);

        return $view->render();
    }

    /**
     * The context box of the Feinwerk promotion kit on a matching Matomo report page (a widget, added by
     * PromoPlacements::addBoxes). Renders nothing in an embed, for users who do not see promotions and on any error.
     */
    public function promoBox(): string
    {
        return PromoPlacements::box(Common::getRequestVar(PromoPlacements::BOX_PARAMETER, '', 'string'));
    }

    /**
     * Kit: the Feinwerk Connector installer page (ConnectorInstaller/ConnectorInstaller.php). Super users only.
     */
    public function connectorInstaller()
    {
        Piwik::checkUserHasSuperUserAccess();
        $env = ConnectorInstaller::environment();
        $redirect = ConnectorInstaller::redirectFor($env);
        if ($redirect !== null) {
            Url::redirectToUrl($redirect);

            return '';
        }

        return $this->renderConnectorInstaller($env, null);
    }

    /**
     * Kit: the click. Matomo's password check first (it comes back here by GET with these parameters), then the nonce
     * (verified and discarded, as Matomo's own install action does), then install when the files are missing, and
     * activate. Success: on to the connector; any failure: back on the page with Matomo's reason.
     */
    public function connectorInstallerRun()
    {
        Piwik::checkUserHasSuperUserAccess();
        $env = ConnectorInstaller::environment();
        $redirect = ConnectorInstaller::redirectFor($env);
        if ($redirect !== null) {
            // active: the connector; switched off: admin home (never the page's "open the connector" fallback)
            Url::redirectToUrl($redirect);

            return '';
        }
        $state = ConnectorInstaller::decide($env);
        if ($state !== ConnectorInstaller::INSTALL && $state !== ConnectorInstaller::ACTIVATE) {
            return $this->renderConnectorInstaller($env, null);
        }

        $nonce = Common::getRequestVar('nonce', '', 'string');
        $verified = StaticContainer::get(\Piwik\Plugins\Login\PasswordVerifier::class)->requirePasswordVerifiedRecently([
            'module' => ConnectorInstaller::PLUGIN,
            'action' => 'connectorInstallerRun',
            'nonce' => $nonce,
        ]);
        if (!$verified) {
            // Matomo is sending the user to its password page (it returns only when its redirect is off)
            return '';
        }

        $error = Nonce::verifyNonceWithErrorMessage(ConnectorInstaller::NONCE, $nonce);
        if ($error !== '') {
            // Matomo's own reason, from its translations (may hold its FAQ link)
            return $this->renderConnectorInstaller($env, ['ok' => false, 'message' => $error, 'html' => true]);
        }
        Nonce::discardNonce(ConnectorInstaller::NONCE);

        $result = ConnectorInstaller::run(
            $env['present'],
            static function (): void {
                (new \Piwik\Plugins\CorePluginsAdmin\PluginInstaller())->installOrUpdatePluginFromMarketplace(ConnectorInstaller::CONNECTOR);
            },
            static function (): void {
                PluginManager::getInstance()->activatePlugin(ConnectorInstaller::CONNECTOR);
            }
        );
        if ($result['ok']) {
            Url::redirectToUrl(ConnectorInstaller::CONNECTOR_URL);
        }

        return $this->renderConnectorInstaller(ConnectorInstaller::environment(), $result);
    }

    /**
     * @param array<string, bool> $env
     * @param array{ok: bool, message: string, html: bool}|null $result
     */
    private function renderConnectorInstaller(array $env, ?array $result): string
    {
        $view = new View('@FeinwerkDeleteVisitsFilter/connectorInstaller');
        // An admin page: no website needed (the menu entry is reached from pages without idSite too). In this
        // ControllerAdmin, setBasicVariablesView() sets the admin variables as well, with Matomo's frame rule for
        // settings pages (enable_framed_settings); a second setBasicVariablesAdminView() would only repeat them.
        $this->setBasicVariablesView($view);
        $state = ConnectorInstaller::decide($env);
        $view->installerState = $state;
        $view->installerError = $result !== null && !$result['ok'] ? $result : null;
        $view->installerNonce = $state === ConnectorInstaller::INSTALL || $state === ConnectorInstaller::ACTIVATE
            ? Nonce::getNonce(ConnectorInstaller::NONCE)
            : '';
        $view->installerLinks = ConnectorInstaller::manualLinks();
        $view->installerWordPressUrl = ConnectorInstaller::wordpressUrl();
        $view->installerConnectorUrl = ConnectorInstaller::CONNECTOR_URL;
        $view->installerPhpFloor = ConnectorInstaller::PHP_FLOOR;
        $view->installerPluginsAdmin = $env['pluginsAdmin'];
        $view->installerConsoleCommand = ConnectorInstaller::CONSOLE_COMMAND;

        return $view->render();
    }

    /**
     * The tip line of the Feinwerk promotion kit under the admin page (this plugin has no reports of its own), after
     * the feedback question; '' where it is not shown and on any error, so it never breaks the page.
     */
    private static function promoLine(int $idSite): string
    {
        try {
            $promo = PromoLine::current($idSite);

            return $promo === null ? '' : PromoLine::render($promo);
        } catch (\Throwable $e) {
            return '';
        }
    }
}
