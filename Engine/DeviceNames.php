<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\Engine;

/**
 * Readable browser and operating system names for the stored codes ("CH" → "Chrome", "LIN" → "GNU/Linux"), through
 * the helpers of Matomo's DevicesDetection plugin (5.0 to 6). Without them the code itself is returned.
 */
final class DeviceNames
{
    public static function browser(string $code): string
    {
        return self::call('getBrowserName', $code);
    }

    public static function os(string $code): string
    {
        return self::call('getOsFullName', $code);
    }

    private static function call(string $function, string $code): string
    {
        if ($code === '') {
            return '';
        }
        $function = 'Piwik\\Plugins\\DevicesDetection\\' . $function;
        if (!function_exists($function)) {
            $file = PIWIK_INCLUDE_PATH . '/plugins/DevicesDetection/functions.php';
            if (!is_file($file)) {
                return $code;
            }
            require_once $file;
        }
        try {
            $name = (string) $function($code);
        } catch (\Throwable $e) {
            return $code;
        }

        return $name === '' ? $code : $name;
    }
}
