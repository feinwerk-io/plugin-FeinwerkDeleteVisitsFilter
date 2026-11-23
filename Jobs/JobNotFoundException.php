<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs;

use Piwik\Http\HttpCodeException;

/** A deletion job that does not exist: Matomo answers the API call with HTTP 404 and the (translated) message. */
final class JobNotFoundException extends \Exception implements HttpCodeException
{
    public function __construct(string $message, ?\Throwable $previous = null)
    {
        parent::__construct($message, 404, $previous);
    }
}
