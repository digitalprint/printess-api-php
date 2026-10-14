<?php

namespace Tests\Printess\API\Exceptions;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Printess\Api\Exceptions\ApiException;
use Tests\Printess\TestHelpers\LinkObjectTestHelpers;

class ApiExceptionTest extends TestCase
{
    use LinkObjectTestHelpers;

    /**
     * @throws ApiException
     */
    public function testCanGetRequestBodyIfRequestIsSet(): void
    {
        $response = new Response(
            422,
            [],
            /** @lang JSON */
            '{
                    "c": 422,
                    "m": "Can not enable Credit card via the API. Please go to the dashboard to enable this payment method.",
                    "_links": {
                         "dashboard": {
                                "href": "https://www.Printess.com/dashboard/settings/profiles/pfl_v9hTwCvYqw/payment-methods",
                                "type": "text/html"
                         },
                         "documentation": {
                                "href": "https://docs.Printess.com/guides/handling-errors",
                                "type": "text/html"
                         }
                    }
                }'
        );

        $request = new Request(
            'POST',
            'https://api.Printess.com/v2/profiles/pfl_v9hTwCvYqw/methods/bancontact',
            [],
            /** @lang JSON */
            '{ "foo": "bar" }'
        );

        $exception = ApiException::createFromResponse($response, $request);

        $this->assertJsonStringEqualsJsonString(/** @lang JSON */'{ "foo": "bar" }', $exception->getRequest()->getBody()->__toString());

        $this->assertStringEndsWith('Request body: { "foo": "bar" }', $exception->getMessage());
        $this->assertStringContainsString('Error executing API call (422): Can not enable Credit card', $exception->getMessage());
    }

    /**
     * @throws ApiException
     */
    public function testCanHandleLegacyErrorResponseFormat(): void
    {
        $response = new Response(
            422,
            [],
            /** @lang JSON */
            '{
                "status": 422,
                "title": "Unprocessable Entity",
                "detail": "Invalid parameters provided."
            }'
        );

        $exception = ApiException::createFromResponse($response);

        $this->assertStringContainsString('Error executing API call (422: Unprocessable Entity): Invalid parameters provided.', $exception->getMessage());
    }

    /**
     * @throws ApiException
     */
    public function testCanHandleEmptyOrUnknownErrorResponse(): void
    {
        $response = new Response(
            500,
            [],
            /** @lang JSON */
            '{}'
        );

        $exception = ApiException::createFromResponse($response);

        $this->assertStringContainsString('Error executing API call', $exception->getMessage());
    }
}
