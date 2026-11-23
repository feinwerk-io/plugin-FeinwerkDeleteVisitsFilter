<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter;

use Piwik\Container\StaticContainer;
use Piwik\Option;
use Piwik\Plugin;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Feedback\PluginFeedback;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\JobStore;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Promo\PromoPlacements;

class FeinwerkDeleteVisitsFilter extends Plugin
{
    public function registerEvents(): array
    {
        return [
            'AssetManager.getStylesheetFiles'        => 'getCssFiles',
            'AssetManager.getJavaScriptFiles'        => 'getJsFiles',
            'Translate.getClientSideTranslationKeys' => 'getClientSideTranslationKeys',
            'Db.getTablesInstalled'                  => 'getTablesInstalled',
            'UsersManager.deleteUser'                => 'onUserDeleted',
            // Feinwerk promotion kit: banner on the admin home, context boxes at the end of matching report pages
            'Template.beforeContent'                 => 'addPromoBanner',
            'Widget.filterWidgets'                   => 'addPromoBoxes',
        ];
    }

    public function install()
    {
        StaticContainer::get(JobStore::class)->install();
    }

    public function uninstall()
    {
        StaticContainer::get(JobStore::class)->uninstall();
        Option::deleteLike(PluginFeedback::THROTTLE_OPTION_PREFIX . '%');
    }

    public function getTablesInstalled(&$allTablesInstalled): void
    {
        $allTablesInstalled[] = JobStore::tableName();
    }

    public function onUserDeleted($userLogin): void
    {
        StaticContainer::get(JobStore::class)->anonymizeCreator((string) $userLogin);
        Option::deleteLike(PluginFeedback::userOptionPattern((string) $userLogin));
    }

    public function getCssFiles(array &$cssFiles): void
    {
        $cssFiles[] = 'plugins/FeinwerkDeleteVisitsFilter/vue/src/Manage/Manage.less';
        $cssFiles[] = 'plugins/FeinwerkDeleteVisitsFilter/vue/src/Running/RunningJobs.less';
        $cssFiles[] = 'plugins/FeinwerkDeleteVisitsFilter/vue/src/Delete/Delete.less';
        $cssFiles[] = 'plugins/FeinwerkDeleteVisitsFilter/vue/src/History/HistoryTab.less';
        $cssFiles[] = 'plugins/FeinwerkDeleteVisitsFilter/vue/src/Manage/FeedbackBar.less';
        $cssFiles[] = 'plugins/FeinwerkDeleteVisitsFilter/stylesheets/promo.css';
        $cssFiles[] = 'plugins/FeinwerkDeleteVisitsFilter/stylesheets/feinwerklogo.css';
    }

    public function getJsFiles(array &$jsFiles): void
    {
        $jsFiles[] = 'plugins/FeinwerkDeleteVisitsFilter/javascripts/promo.js';
    }

    /**
     * Template.beforeContent: the banner of the Feinwerk promotion kit at the top of Matomo's admin home, once per page
     * across all Feinwerk plugins (Template.adminHome renders far below the Marketplace blocks; Peter, 2026-11-13).
     */
    public function addPromoBanner(&$out, $type = null, $module = null, $action = null): void
    {
        if (PromoPlacements::isAdminHome($type, $module, $action)) {
            PromoPlacements::adminHome($out);
        }
    }

    /** Widget.filterWidgets: the context boxes of the Feinwerk promotion kit at the end of matching report pages. */
    public function addPromoBoxes($list): void
    {
        PromoPlacements::addBoxes($list);
    }

    public function getClientSideTranslationKeys(array &$translationKeys): void
    {
        // Everything in lang/en.json: the admin page is the only client of these texts.
        $translations = json_decode((string) file_get_contents(__DIR__ . '/lang/en.json'), true);
        foreach (array_keys($translations['FeinwerkDeleteVisitsFilter'] ?? []) as $key) {
            $translationKeys[] = 'FeinwerkDeleteVisitsFilter_' . $key;
        }
    }
}
