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

    public function test_englishAndGermanHaveTheSameKeys(): void
    {
        $english = self::keys('en');
        $german = self::keys('de');
        sort($english);
        sort($german);

        $this->assertSame($english, $german);
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
        $file = dirname(__DIR__, 2) . "/lang/{$language}.json";
        $data = json_decode((string) file_get_contents($file), true);

        return array_keys($data['FeinwerkDeleteVisitsFilter'] ?? []);
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
