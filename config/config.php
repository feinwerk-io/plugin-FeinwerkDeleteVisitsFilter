<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Clock;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\ArchiveInvalidation;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\MatomoArchiveInvalidation;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\MatomoSegmentChecker;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\MatomoSiteDirectory;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\SegmentChecker;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\SiteDirectory;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\VisitDeleter;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\VisitFinder;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\VisitPurger;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine\VisitSource;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\EventDispatcher;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\JobRepository;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\JobStore;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\MatomoEventDispatcher;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\MatomoRunnerLock;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\RunnerLock;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\SystemClock;

return [
    Clock::class => \Piwik\DI::autowire(SystemClock::class),
    JobRepository::class => \Piwik\DI::get(JobStore::class),
    SiteDirectory::class => \Piwik\DI::autowire(MatomoSiteDirectory::class),
    SegmentChecker::class => \Piwik\DI::autowire(MatomoSegmentChecker::class),
    VisitSource::class => \Piwik\DI::get(VisitFinder::class),
    VisitPurger::class => \Piwik\DI::get(VisitDeleter::class),
    ArchiveInvalidation::class => \Piwik\DI::autowire(MatomoArchiveInvalidation::class),
    RunnerLock::class => \Piwik\DI::autowire(MatomoRunnerLock::class),
    EventDispatcher::class => \Piwik\DI::autowire(MatomoEventDispatcher::class),
];
