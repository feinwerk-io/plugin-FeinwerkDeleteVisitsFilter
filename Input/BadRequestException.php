<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\Input;

use Piwik\Http\HttpCodeException;

/**
 * Invalid input to an API method: Matomo answers with HTTP 400 and the (translated) message. Own class instead of core's
 * BadRequestException, whose constructor drops the code on Matomo 5.0.0 (the answer would stay 200 there).
 */
final class BadRequestException extends \Exception implements HttpCodeException
{
    public function __construct(string $message, ?\Throwable $previous = null)
    {
        parent::__construct($message, 400, $previous);
    }
}
