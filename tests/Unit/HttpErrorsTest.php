<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\FeinwerkDeleteVisitsFilter\tests\Unit;

use PHPUnit\Framework\TestCase;
use Piwik\Http\HttpCodeException;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Input\BadRequestException;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Input\HttpErrorStatus;
use Piwik\Plugins\FeinwerkDeleteVisitsFilter\Jobs\JobNotFoundException;

/** Matomo sets the API answer's HTTP status from an HttpCodeException with a code above 0 (core API ResponseBuilder). */
class HttpErrorsTest extends TestCase
{
    public function test_invalid_input_is_a_400_with_its_message(): void
    {
        $previous = new \InvalidArgumentException('raw');
        $e = new BadRequestException('Please enter a valid date.', $previous);

        self::assertInstanceOf(HttpCodeException::class, $e);
        self::assertSame(400, $e->getCode());
        self::assertSame('Please enter a valid date.', $e->getMessage());
        self::assertSame($previous, $e->getPrevious());
    }

    public function test_an_unknown_job_is_a_404(): void
    {
        $e = new JobNotFoundException('Deletion 7 does not exist.');

        self::assertInstanceOf(HttpCodeException::class, $e);
        self::assertSame(404, $e->getCode());
    }

    /**
     * The plugin's own admin page (Matomo's AjaxHelper sends force_api_session=1) keeps HTTP 200: for any error status
     * AjaxHelper replaces the API's message with "Something went wrong", and the page could not say what went wrong.
     */
    public function test_the_plugins_own_page_keeps_http_200(): void
    {
        unset($_GET['force_api_session'], $_POST['force_api_session']);
        self::assertTrue(HttpErrorStatus::wanted(), 'scripts and integrations');

        $_POST['force_api_session'] = '1';
        try {
            self::assertFalse(HttpErrorStatus::wanted(), 'the admin page');
        } finally {
            unset($_POST['force_api_session']);
        }
    }

    /** Matomo's API lists and serves every public method of the API class, static ones too (core API Proxy). */
    public function test_the_api_class_declares_no_public_static_helpers(): void
    {
        $class = new \ReflectionClass(\Piwik\Plugins\FeinwerkDeleteVisitsFilter\API::class);
        $own = array_filter(
            $class->getMethods(\ReflectionMethod::IS_PUBLIC),
            fn(\ReflectionMethod $method) => $method->isStatic() && $method->getDeclaringClass()->getName() === $class->getName()
        );

        self::assertSame([], array_map(fn(\ReflectionMethod $method) => $method->getName(), array_values($own)));
    }
}
