<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Unit;

use PHPUnit\Framework\TestCase;

class TranslationsTest extends TestCase
{
    private const SOURCE_DIRS = ['', 'Activity', 'Commands', 'Engine', 'Input', 'Jobs', 'Ui', 'templates', 'vue/src'];

    /** Languages every Feinwerk plugin ships (Peter, 2026-11-19: "bitte alle Plugins mindestens in französisch, spanisch, italienisch, deutsch, englisch ausliefern"). */
    private const LANGUAGES = ['en', 'de', 'fr', 'es', 'it'];

    public function test_everyLanguageShipsWithTheEnglishKeysInTheirOrder(): void
    {
        $english = self::keys('en');
        foreach (self::LANGUAGES as $language) {
            $this->assertFileExists(dirname(__DIR__, 2) . "/lang/{$language}.json");
            $this->assertSame($english, self::keys($language), "keys and their order in {$language}.json");
        }
        foreach (glob(dirname(__DIR__, 2) . '/lang/*.json') ?: [] as $file) {
            $this->assertSame($english, self::keys(basename($file, '.json')), 'keys and their order in ' . basename($file));
        }
    }

    public function test_everyTranslationHasTheEnglishPlaceholders(): void
    {
        $placeholders = static function (string $text): array {
            preg_match_all('/%(?:\d+\$)?s/', $text, $matches);
            $found = $matches[0];
            sort($found);

            return $found;
        };
        $english = self::texts('en');
        foreach (self::LANGUAGES as $language) {
            foreach (self::texts($language) as $key => $text) {
                $this->assertStringNotContainsString('\\$', $text, "{$language} {$key}");
                $this->assertSame($placeholders($english[$key]), $placeholders($text), "{$language} {$key}");
            }
        }
    }

    public function test_noTranslationHasADashOrAUrl(): void
    {
        foreach (self::LANGUAGES as $language) {
            foreach (self::texts($language) as $key => $text) {
                $this->assertSame(0, preg_match('/[\x{2013}\x{2014}]/u', $text), "dash in {$language} {$key}");
                $this->assertSame(0, preg_match('~https?://|www\.~', $text), "URL in {$language} {$key}");
            }
        }
    }

    public function test_everyKeyUsedInTheCodeExistsInEnglish(): void
    {
        $defined = self::keys('en');
        $missing = [];
        foreach (self::sourceFiles() as $file) {
            preg_match_all('/FeinwerkDeleteVisitsFilter_([A-Za-z0-9_]+)/', (string) file_get_contents($file), $matches);
            foreach ($matches[1] as $key) {
                if (!in_array($key, $defined, true)) {
                    $missing[] = basename($file) . ': ' . $key;
                }
            }
        }

        $this->assertSame([], array_values(array_unique($missing)));
    }

    /**
     * @return string[]
     */
    private static function keys(string $language): array
    {
        return array_keys(self::texts($language));
    }

    /**
     * @return array<string, string>
     */
    private static function texts(string $language): array
    {
        $file = dirname(__DIR__, 2) . "/lang/{$language}.json";
        $data = json_decode((string) file_get_contents($file), true);

        return $data['FeinwerkDeleteVisitsFilter'] ?? [];
    }

    /**
     * @return string[]
     */
    private static function sourceFiles(): array
    {
        $root = dirname(__DIR__, 2);
        $files = [];
        foreach (self::SOURCE_DIRS as $dir) {
            $path = rtrim($root . '/' . $dir, '/');
            if (!is_dir($path)) {
                continue;
            }
            $iterator = $dir === ''
                ? new \ArrayIterator(array_map(fn(string $name) => new \SplFileInfo($path . '/' . $name), scandir($path)))
                : new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS));
            foreach ($iterator as $file) {
                if ($file->isFile() && in_array($file->getExtension(), ['php', 'twig', 'ts', 'vue'], true)) {
                    $files[] = $file->getPathname();
                }
            }
        }

        return $files;
    }
}
