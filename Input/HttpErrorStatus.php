<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\Input;

use Piwik\Common;

/**
 * Whether API errors carry an HTTP status (400, 404). Scripts and integrations get it. Calls of the plugin's own admin
 * page keep HTTP 200: Matomo's AjaxHelper sends them with force_api_session=1 and, for any error status, replaces the
 * API's message with "Something went wrong" (Matomo 5.0 to 6.0), so the page could no longer say what went wrong.
 * Not on the API class: Matomo's API lists and serves every public method there, static ones too.
 */
final class HttpErrorStatus
{
    public static function wanted(): bool
    {
        return Common::getRequestVar('force_api_session', 0, 'int') !== 1;
    }
}
