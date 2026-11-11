<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\Ui;

use Piwik\Common;
use Piwik\Container\StaticContainer;
use Piwik\Plugin\Manager as PluginManager;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Feedback\PluginFeedback;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\Job;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\JobRepository;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\SystemSettings;
use Piwik\Plugins\SitesManager\Model as SitesModel;
use Piwik\Translation\Translator;

/**
 * Start data of the admin page (superusers only): website names, running jobs, settings and the feedback question.
 */
class AdminPageProps
{
    /** From this many matching visits the confirmation asks to type the number. */
    public const CONFIRM_THRESHOLD = 10000;

    public function __construct(private JobRepository $jobs)
    {
    }

    /**
     * @return array{initialSiteId: int, siteNames: object, activeJobs: list<array>, confirmThreshold: int, backgroundBudget: int, defaultBatchSize: int, feedback: array{enabled: bool, supportUrl: string, version: string}}
     */
    public function build(int $requestedIdSite): array
    {
        $siteNames = [];
        foreach ((new SitesModel())->getAllSites() as $site) {
            // Stored HTML-encoded (as entered in the admin UI); the page escapes them itself.
            $siteNames[(int) $site['idsite']] = Common::unsanitizeInputValue((string) $site['name']);
        }
        ksort($siteNames);
        $idSite = isset($siteNames[$requestedIdSite]) ? $requestedIdSite : (int) (array_key_first($siteNames) ?? 0);
        $settings = new SystemSettings();

        return [
            'initialSiteId' => $idSite,
            // An object so JSON keeps the ids as keys (also for an empty list).
            'siteNames' => (object) $siteNames,
            'activeJobs' => array_map(fn(Job $job) => $job->toApiArray(), $this->jobs->findActive()),
            'confirmThreshold' => self::CONFIRM_THRESHOLD,
            'backgroundBudget' => (int) $settings->backgroundTimeBudget->getValue(),
            'defaultBatchSize' => (int) $settings->defaultBatchSize->getValue(),
            'feedback' => self::feedback(),
        ];
    }

    /**
     * The feedback question's switch, support link and plugin version (vue/src/Manage/FeedbackBar.vue). A failure here
     * never breaks the page: the question is then left out.
     *
     * @return array{enabled: bool, supportUrl: string, version: string}
     */
    private static function feedback(): array
    {
        try {
            return [
                'enabled' => PluginFeedback::enabled(),
                'supportUrl' => PluginFeedback::supportUrl((string) StaticContainer::get(Translator::class)->getCurrentLanguage()),
                'version' => (string) PluginManager::getInstance()->getLoadedPlugin(PluginFeedback::PLUGIN)->getVersion(),
            ];
        } catch (\Throwable $e) {
            return ['enabled' => false, 'supportUrl' => '', 'version' => ''];
        }
    }
}
