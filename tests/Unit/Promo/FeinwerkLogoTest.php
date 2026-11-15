<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Unit\Promo;

use PHPUnit\Framework\TestCase;

/**
 * The Feinwerk logo in banners (Peter, 2026-11-13: "in die banner, wo möglich bitte generell noch das feinwerk-logo
 * unterbringen"; docs/promotions.md): one shared partial, nothing loaded, no font dependency, hidden from screen readers,
 * in the banner, the boxes and the section tile, never in the one-line footer, the tip line or the dashboard card.
 */
class FeinwerkLogoTest extends TestCase
{
    private const INCLUDE = "{% include '@FeinwerkDeleteVisitsFilter/_feinwerkLogo.twig' %}";

    public function test_the_logo_loads_nothing_and_needs_no_font(): void
    {
        $logo = $this->file('templates/_feinwerkLogo.twig');

        self::assertStringContainsString('aria-hidden="true"', $logo);
        self::assertStringContainsString('<svg class="fwlogo__mark"', $logo);
        self::assertSame(0, preg_match('/<text\b|<image\b|<img\b|\bhref=|\bsrc=|url\(/', $logo), 'inline only');
        self::assertSame(0, preg_match('/\burl\(|@import|@font-face/', $this->file('stylesheets/feinwerklogo.css')));
    }

    public function test_banner_and_boxes_show_it_the_tip_line_and_the_feedback_row_do_not(): void
    {
        foreach (['templates/_promoBox.twig'] as $with) {
            self::assertSame(1, substr_count($this->file($with), self::INCLUDE), $with);
        }
        foreach (['templates/_promo.twig', 'vue/src/Manage/FeedbackBar.vue'] as $without) {
            self::assertStringNotContainsString('_feinwerkLogo', $this->file($without), $without);
        }
    }

    public function test_light_and_dark_use_the_colours_of_the_store_logo(): void
    {
        $css = $this->file('stylesheets/feinwerklogo.css');

        // feinwerk-plugins-logo.svg (light) and -logo-white.svg (dark)
        foreach (['#0e7c7b', '#1a1714', '#6b6359', '#9fd8d2', '#e8e6e1', '#6b7180'] as $colour) {
            self::assertStringContainsString($colour, $css);
        }
        self::assertStringContainsString('[data-theme-mode="dark"] .fwlogo', $css);
        self::assertStringContainsString('[data-theme-mode="auto"] .fwlogo', $css);
    }

    private function file(string $path): string
    {
        return (string) file_get_contents(__DIR__ . '/../../../' . $path);
    }
}
