<?php

namespace Tests\Printess\API\Exceptions;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Printess\Api\Exceptions\ApiException;
use PHPUnit\Framework\TestCase;
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
                    "status": 422,
                    "title": "Unprocessable Entity",
                    "detail": "Can not enable Credit card via the API. Please go to the dashboard to enable this payment method.",
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
        $this->assertStringEndsWith('Error executing API call (422: Unprocessable Entity): Can not enable Credit card via the API. Please go to the dashboard to enable this payment method.. Documentation: https://docs.Printess.com/guides/handling-errors. Request body: { "foo": "bar" }', $exception->getMessage());
    }
}
