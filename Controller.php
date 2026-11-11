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
use Piwik\Piwik;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Promo\PromoLine;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Promo\PromoPlacements;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Ui\AdminPageProps;
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
