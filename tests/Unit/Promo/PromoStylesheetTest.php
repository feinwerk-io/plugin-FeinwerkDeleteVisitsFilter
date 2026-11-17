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
 * stylesheets/promo.css keeps every notice hidden until promo.js has checked the dismissal and the one-per-page rule
 * (`.fwpromo-line:not([data-fwpromo-ready])`). A rule that shows a notice and weighs as much or more must require
 * `[data-fwpromo-ready]`, or it beats the hidden rule and the notice shows before the check (the first footer variant A
 * had `.fwpromo-line[data-fwpromo-placement="line"] { display: flex }`, docs/footer.md).
 */
class PromoStylesheetTest extends TestCase
{
    private const HIDDEN_WEIGHT = [0, 2];

    public function test_no_rule_shows_a_notice_before_promo_js_has_checked_it(): void
    {
        $css = (string) file_get_contents(__DIR__ . '/../../../stylesheets/promo.css');

        $this->assertStringContainsString('.fwpromo-line:not([data-fwpromo-ready])', $css);
        $this->assertSame([], self::earlyShowingSelectors($css));
    }

    public function test_the_tip_line_still_outranks_the_plain_class_of_older_kit_copies(): void
    {
        $css = (string) file_get_contents(__DIR__ . '/../../../stylesheets/promo.css');

        $this->assertSame(1, preg_match('/\.fwpromo-line\[data-fwpromo-ready\]\s*\{[^}]*flex-wrap:\s*nowrap/', $css));
    }

    public function test_the_check_catches_a_showing_rule_as_heavy_as_the_hidden_rule(): void
    {
        $css = ".fwpromo-line:not([data-fwpromo-ready]) { display: none; }\n"
            . ".fwpromo-line, .fwpromo-line[data-fwpromo-placement=\"line\"] { display: flex; }\n"
            . ".matomo-widget .fwpromo-box { display: block; }\n"
            . ".fwpromo-box, .fwpromo-line[data-fwpromo-ready], .x .fwpromo-line__text { display: flex; }\n";

        $this->assertSame(
            ['.fwpromo-line[data-fwpromo-placement="line"]', '.matomo-widget .fwpromo-box'],
            self::earlyShowingSelectors($css)
        );
    }

    /**
     * Selectors whose subject is a notice (`.fwpromo-line` or `.fwpromo-box`), that set a display other than none, weigh
     * at least as much as the hidden rule and do not require `[data-fwpromo-ready]` on the notice.
     *
     * @return string[]
     */
    private static function earlyShowingSelectors(string $css): array
    {
        $css = (string) preg_replace('~/\*.*?\*/~s', '', $css);
        preg_match_all('/([^{}]+)\{([^{}]*)\}/', $css, $rules, PREG_SET_ORDER);

        $found = [];
        foreach ($rules as $rule) {
            if (!preg_match('/(^|;)\s*display\s*:\s*(?!none\b)[a-z-]+/i', $rule[2])) {
                continue;
            }
            foreach (explode(',', $rule[1]) as $selector) {
                $selector = trim($selector);
                $parts = preg_split('/\s*[\s>+~]\s*(?![^\[]*\])/', $selector);
                $subject = (string) end($parts);
                if (!preg_match('/^\.fwpromo-(line|box)(?![\w-])/', $subject)) {
                    continue;
                }
                if (strpos($subject, '[data-fwpromo-ready]') !== false) {
                    continue;
                }
                if (self::weight($selector) >= self::HIDDEN_WEIGHT) {
                    $found[] = $selector;
                }
            }
        }

        return $found;
    }

    /**
     * Specificity as [ids, classes + attributes + pseudo-classes]; `:not()` itself counts nothing, its argument does.
     *
     * @return int[]
     */
    private static function weight(string $selector): array
    {
        $ids = preg_match_all('/#[\w-]+/', $selector);
        $attributes = preg_match_all('/\[[^\]]*\]/', $selector);
        $plain = (string) preg_replace('/\[[^\]]*\]/', '', $selector);
        $classes = preg_match_all('/\.[\w-]+/', $plain);
        $pseudo = preg_match_all('/(?<!:):(?!not\()[\w-]+/', $plain);

        return [$ids, $classes + $attributes + $pseudo];
    }

    /**
     * Footer A keeps the × right after the link although another plugin's merged CSS positions it absolutely at the
     * right edge of a padded tip line (Tracking Spam Score 5.0.1: `.fwpromo-line { position: relative; padding-right:
     * 24px }`, `.fwpromo-line__dismiss { position: absolute; top: 0; right: 0 }`). The defence must weigh more than
     * those class-only rules, so the load order does not matter.
     */
    public function test_footer_a_outranks_foreign_positioning_of_the_hide_control(): void
    {
        $css = (string) file_get_contents(__DIR__ . '/../../../stylesheets/promo.css');
        $css = (string) preg_replace('~/\*.*?\*/~s', '', $css);

        $this->assertSame(1, preg_match('/(^|\})\s*\.fwpromo-line\[data-fwpromo-ready\]\s*\{[^}]*position:\s*static[^}]*\}/', $css));
        $this->assertSame(1, preg_match('/(^|\})\s*\.fwpromo-line\[data-fwpromo-ready\]\s*\{[^}]*padding:\s*0\s*;[^}]*\}/', $css));
        $this->assertSame(1, preg_match('/(^|\})\s*\.fwpromo-line\[data-fwpromo-ready\]\s+\.fwpromo-line__dismiss\s*\{[^}]*position:\s*static/', $css));
        $this->assertGreaterThan(self::weight('.fwpromo-line'), self::weight('.fwpromo-line[data-fwpromo-ready]'));
        $this->assertGreaterThan(self::weight('.fwpromo-line__dismiss'), self::weight('.fwpromo-line[data-fwpromo-ready] .fwpromo-line__dismiss'));
    }

    /** The tip line's link keeps link colour and underline; the box foot's flex rule never catches it (merged CSS of all plugins). */
    public function test_the_tip_link_is_a_link_not_a_flex_row(): void
    {
        $css = (string) file_get_contents(__DIR__ . '/../../../stylesheets/promo.css');
        $css = (string) preg_replace('~/\*.*?\*/~s', '', $css);

        $this->assertSame(1, preg_match('/\.fwpromo-line__link\s*,\s*\.fwpromo-box__link\s*\{[^}]*text-decoration:\s*underline/', $css));
        preg_match_all('/([^{}]+)\{([^}]*)\}/', $css, $rules, PREG_SET_ORDER);
        foreach ($rules as $rule) {
            if (strpos($rule[1], '.fwpromo-line__link') !== false) {
                $this->assertStringNotContainsString('display: flex', $rule[2], trim($rule[1]));
            }
        }
    }

}
