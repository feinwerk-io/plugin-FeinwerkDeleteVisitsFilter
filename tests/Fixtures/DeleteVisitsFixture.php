<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Fixtures;

use Matomo\Network\IPUtils;
use Piwik\Common;
use Piwik\Date;
use Piwik\Db;
use Piwik\Plugins\PrivacyManager\IPAnonymizer;
use Piwik\Tests\Framework\Fixture;

/**
 * Two websites (UTC and Europe/Berlin) with bot and human visits, pageviews and ecommerce orders, plus a third UTC website
 * whose visits share the same last action time (bots often hit within the same second).
 */
class DeleteVisitsFixture extends Fixture
{
    public const BOT_IP = '192.0.2.10';
    public const HUMAN_IP = '198.51.100.20';

    public $dateTime = '2026-03-14 06:00:00';

    public function setUp(): void
    {
        // IntegrationTestCase sets createSuperUser=false; tracking with cdt/cip needs a valid super user token
        // (same as core's PrivacyManager MultipleSitesMultipleVisitsFixture).
        self::createSuperUser(false);

        // IntegrationTestCase turns configureComponents off, so Matomo's default IP anonymization (2 bytes) would store
        // 192.0.0.0 / 198.51.0.0 instead of the tracked addresses (core's PrivacyManager fixture does the same).
        IPAnonymizer::deactivate();

        if (!self::siteCreated(1)) {
            self::createWebsite('2026-01-01 00:00:00', 1, 'UTC shop', 'http://utc.example', 1, null, null, 'UTC');
        }
        if (!self::siteCreated(2)) {
            self::createWebsite('2026-01-01 00:00:00', 1, 'Berlin shop', 'http://berlin.example', 1, null, null, 'Europe/Berlin');
        }
        if (!self::siteCreated(3)) {
            self::createWebsite('2026-01-01 00:00:00', 1, 'Tie shop', 'http://tie.example', 1, null, null, 'UTC');
        }

        $this->trackVisit(1, '2026-03-14 08:00:00', self::BOT_IP, ['/a', '/spam']);
        $this->trackVisit(1, '2026-03-14 09:00:00', self::BOT_IP, ['/a']);
        $this->trackVisit(1, '2026-03-15 09:00:00', self::BOT_IP, ['/a']);
        $this->trackVisit(1, '2026-03-14 10:00:00', self::HUMAN_IP, ['/a', '/b'], true);
        $this->trackVisit(1, '2026-03-14 11:00:00', self::HUMAN_IP, ['/spam']);

        $this->trackVisit(2, '2026-03-14 22:30:00', self::BOT_IP, ['/a']);
        $this->trackVisit(2, '2026-03-14 23:30:00', self::BOT_IP, ['/a']);
        $this->trackVisit(2, '2026-03-14 12:00:00', self::HUMAN_IP, ['/a'], true);

        // Three visits with the identical visit_last_action_time (the idvisit tiebreaker decides the order) and a later one.
        // The IPs differ on purpose: Matomo merges same-fingerprint requests of the same second into ONE visit.
        $this->trackVisit(3, '2026-03-14 12:00:00', self::BOT_IP, ['/a']);
        $this->trackVisit(3, '2026-03-14 12:00:00', self::HUMAN_IP, ['/a']);
        $this->trackVisit(3, '2026-03-14 12:00:00', '203.0.113.30', ['/a']);
        $this->trackVisit(3, '2026-03-14 13:00:00', self::BOT_IP, ['/a']);
    }

    /**
     * One new visit; one pageview per path, one minute apart, optionally followed by an ecommerce order.
     */
    private function trackVisit(int $idSite, string $startUtc, string $ip, array $paths, bool $order = false): void
    {
        $tracker = self::getTracker($idSite, $startUtc, true, true);
        $tracker->setNewVisitorId();
        $tracker->setIp($ip);
        foreach (array_values($paths) as $i => $path) {
            $tracker->setForceVisitDateTime(Date::factory($startUtc)->addPeriod($i, 'minute')->getDatetime());
            $tracker->setUrl('http://example.com' . $path);
            self::checkResponse($tracker->doTrackPageView('Page ' . $path));
        }
        if ($order) {
            $tracker->addEcommerceItem('SKU-1', 'Item', 'Category', 10.0, 1);
            self::checkResponse($tracker->doTrackEcommerceOrder('order-' . $idSite . '-' . md5($startUtc), 10.0));
        }
    }

    /**
     * @return int[] ascending
     */
    public static function visitIds(int $idSite, ?string $ip = null): array
    {
        $sql = 'SELECT idvisit FROM ' . Common::prefixTable('log_visit') . ' WHERE idsite = ?';
        $bind = [$idSite];
        if ($ip !== null) {
            $sql .= ' AND location_ip = ?';
            $bind[] = IPUtils::stringToBinaryIP($ip);
        }

        return array_map('intval', array_column(Db::fetchAll($sql . ' ORDER BY idvisit ASC', $bind), 'idvisit'));
    }

    public static function visitIdAt(int $idSite, string $lastActionUtc): int
    {
        return (int) Db::fetchOne(
            'SELECT idvisit FROM ' . Common::prefixTable('log_visit') . ' WHERE idsite = ? AND visit_last_action_time = ?',
            [$idSite, $lastActionUtc]
        );
    }
}
