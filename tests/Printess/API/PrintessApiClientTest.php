<?php

namespace Tests\Printess\Api;

use Eloquent\Liberator\Liberator;
use Eloquent\Liberator\LiberatorProxyInterface;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Printess\Api\Exceptions\ApiException;
use Printess\Api\Exceptions\IncompatiblePlatform;
use Printess\Api\Exceptions\UnrecognizedClientException;
use Printess\Api\HttpAdapter\Guzzle6And7PrintessHttpAdapter;
use Printess\Api\PrintessApiClient;
use Tests\Printess\TestHelpers\FakeHttpAdapter;
use function serialize;

class PrintessApiClientTest extends TestCase
{
    /**
     * @var ClientInterface|MockObject
     */
    private $guzzleClient;

    /**
     * @var PrintessApiClient
     */
    private $printessApiClient;

    private $apiKey;

    /**
     * @throws ApiException
     * @throws IncompatiblePlatform
     * @throws UnrecognizedClientException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->apiKey = str_repeat("X", 1024);

        $this->guzzleClient = $this->createMock(Client::class);
        $this->printessApiClient = new PrintessApiClient($this->guzzleClient);

        $this->printessApiClient->setApiKey($this->apiKey);
    }

    /**
     * @throws ApiException
     */
    public function testPerformHttpCallReturnsBodyAsObject(): void
    {
        $response = new Response(200, [], '{"resource": "payment"}');

        $this->guzzleClient
            ->expects($this->once())
            ->method('send')
            ->willReturn($response);


        $parsedResponse = $this->printessApiClient->performHttpCall('GET', '');

        $this->assertEquals(
            (object)['resource' => 'payment'],
            $parsedResponse
        );
    }

    public function testPerformHttpCallCreatesApiExceptionCorrectly(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Error executing API call (422: Unprocessable Entity): Non-existent parameter "recurringType" for this API call. Did you mean: "sequenceType"?');
        $this->expectExceptionCode(422);

        $response = new Response(422, [], '{
            "status": 422,
            "title": "Unprocessable Entity",
            "detail": "Non-existent parameter \"recurringType\" for this API call. Did you mean: \"sequenceType\"?",
            "field": "recurringType",
            "_links": {
                "documentation": {
                    "href": "https://api.printess.com/swagger/index.html",
                    "type": "text/html"
                }
            }
        }');

        $this->guzzleClient
            ->expects($this->once())
            ->method('send')
            ->willReturn($response);

        try {
            $this->printessApiClient->performHttpCall('GET', '');
        } catch (ApiException $e) {
            $this->assertEquals('recurringType', $e->getField());
            $this->assertEquals('https://api.printess.com/swagger/index.html', $e->getDocumentationUrl());
            $this->assertEquals($response, $e->getResponse());

            throw $e;
        }
    }

    public function testPerformHttpCallCreatesApiExceptionWithoutFieldAndDocumentationUrl(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Error executing API call (422: Unprocessable Entity): Non-existent parameter "recurringType" for this API call. Did you mean: "sequenceType"?');
        $this->expectExceptionCode(422);

        $response = new Response(422, [], '{
            "status": 422,
            "title": "Unprocessable Entity",
            "detail": "Non-existent parameter \"recurringType\" for this API call. Did you mean: \"sequenceType\"?"
        }');

        $this->guzzleClient
            ->expects($this->once())
            ->method('send')
            ->willReturn($response);

        try {
            $this->printessApiClient->performHttpCall('GET', '');
        } catch (ApiException $e) {
            $this->assertNull($e->getField());
            $this->assertNull($e->getDocumentationUrl());
            $this->assertEquals($response, $e->getResponse());

            throw $e;
        }
    }

    public function testCanBeSerializedAndUnserialized(): void
    {
        $this->printessApiClient->setApiEndpoint("https://myprintessproxy.local");
        $serialized = serialize($this->printessApiClient);

        $this->assertStringNotContainsString('test_foobarfoobarfoobarfoobarfoobar', $serialized, "API key should not be in serialized data or it will end up in caches.");

        /** @var PrintessApiClient|LiberatorProxyInterface $client_copy */
        $client_copy = Liberator::liberate(unserialize($serialized));

        $this->assertEmpty($client_copy->apiKey, "API key should not have been remembered");
        $this->assertInstanceOf(Guzzle6And7PrintessHttpAdapter::class, $client_copy->httpClient, "A Guzzle client should have been set.");
        $this->assertNull($client_copy->usesOAuth());
        $this->assertEquals("https://myprintessproxy.local", $client_copy->getApiEndpoint(), "The API endpoint should be remembered");

        $this->assertNotEmpty($client_copy->production);
    }

    /**
     * @throws ApiException
     */
    public function testResponseBodyCanBeReadMultipleTimesIfMiddlewareReadsItFirst(): void
    {
        $response = new Response(200, [], '{"resource": "directory"}');

        // Before the PrintessApiClient gets the response, some middleware reads the body first.
        $bodyAsReadFromMiddleware = (string)$response->getBody();

        $this->guzzleClient
            ->expects($this->once())
            ->method('send')
            ->willReturn($response);

        $parsedResponse = $this->printessApiClient->performHttpCall('GET', '');

        $this->assertEquals(
            '{"resource": "directory"}',
            $bodyAsReadFromMiddleware
        );

        $this->assertEquals(
            (object)['resource' => 'directory'],
            $parsedResponse
        );
    }

    /**
     * This test verifies that our request headers are correctly sent to Printess.
     * If these are broken, it could be that some payments do not work.
     *
     * @throws ApiException
     */
    public function testCorrectRequestHeaders(): void
    {
        $response = new Response(200, [], '{"resource": "directory"}');
        $fakeAdapter = new FakeHttpAdapter($response);

        $printessClient = new PrintessApiClient($fakeAdapter);
        $printessClient->setApiKey($this->apiKey);

        $printessClient->performHttpCallToFullUrl('GET', '', '');

        $usedHeaders = $fakeAdapter->getUsedHeaders();

        # these change through environments
        # just make sure its existing
        $this->assertArrayHasKey('User-Agent', $usedHeaders);
        $this->assertArrayHasKey('X-Printess-Client-Info', $usedHeaders);

        # these should be exactly the expected values
        $this->assertEquals('Bearer ' . $this->apiKey, $usedHeaders['Authorization']);
        $this->assertEquals('application/json', $usedHeaders['Accept']);
        $this->assertEquals('application/json', $usedHeaders['Content-Type']);
    }

    /**
     * This test verifies that we do not add a Content-Type request header
     * if we do not send a BODY (skipping argument).
     * In this case it has to be skipped.
     *
     * @throws ApiException
     * @throws IncompatiblePlatform
     * @throws UnrecognizedClientException
     */
    public function testNoContentTypeWithoutProvidedBody(): void
    {
        $response = new Response(200, [], '{"resource": "payment"}');
        $fakeAdapter = new FakeHttpAdapter($response);

        $printessClient = new PrintessApiClient($fakeAdapter);
        $printessClient->setApiKey($this->apiKey);

        $printessClient->performHttpCallToFullUrl('GET', '');

        $this->assertEquals(false, isset($fakeAdapter->getUsedHeaders()['Content-Type']));
    }
}
