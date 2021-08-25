<?php

namespace Tests\Printess\Api\Endpoints;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Printess\Api\Exceptions\ApiException;
use Printess\Api\Exceptions\IncompatiblePlatform;
use Printess\Api\Exceptions\UnrecognizedClientException;
use Printess\Api\PrintessApiClient;

abstract class BaseEndpointTest extends TestCase
{
    /**
     * @var Client|MockObject
     */
    protected $guzzleClient;

    /**
     * @var PrintessApiClient
     */
    protected $apiClient;

    /**
     * @throws IncompatiblePlatform
     * @throws ApiException
     * @throws UnrecognizedClientException
     */
    protected function mockApiCall(Request $expectedRequest, Response $response, $oAuthClient = false): void
    {
        $this->guzzleClient = $this->createMock(Client::class);

        $this->apiClient = new PrintessApiClient($this->guzzleClient);

        $apiKey = str_repeat("X", 1024);

        if (! $oAuthClient) {
            $this->apiClient->setApiKey($apiKey);
        } else {
            $this->apiClient->setAccessToken($apiKey);
        }

        $this->guzzleClient
            ->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf(Request::class))
            ->willReturnCallback(function (Request $request) use ($expectedRequest, $response) {
                $this->assertEquals($expectedRequest->getMethod(), $request->getMethod(), "HTTP method must be identical");

                $this->assertEquals(
                    $expectedRequest->getUri()->getPath(),
                    $request->getUri()->getPath(),
                    "URI path must be identical"
                );

                $this->assertEquals(
                    $expectedRequest->getUri()->getQuery(),
                    $request->getUri()->getQuery(),
                    'Query string parameters must be identical'
                );

                $requestBody = $request->getBody()->getContents();
                $expectedBody = $expectedRequest->getBody()->getContents();

                if ($expectedBody !== '' && $requestBody !== '') {
                    $this->assertJsonStringEqualsJsonString(
                        $expectedBody,
                        $requestBody,
                        "HTTP body must be identical"
                    );
                }

                return $response;
            });
    }

    protected function copy($array, $object)
    {
        foreach ($array as $property => $value) {
            $object->$property = $value;
        }

        return $object;
    }
}
