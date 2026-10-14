<?php

namespace Tests\Printess\API\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Printess\Api\Exceptions\ApiException;
use Printess\Api\Exceptions\IncompatiblePlatform;
use Printess\Api\Exceptions\UnrecognizedClientException;
use Printess\Api\Resources\SaveToken;
use Tests\Printess\TestHelpers\LinkObjectTestHelpers;

class SaveTokenEndpointTest extends BaseEndpointTest
{
    use LinkObjectTestHelpers;

    /**
     * @throws IncompatiblePlatform
     * @throws ApiException
     * @throws UnrecognizedClientException
     */
    public function testExtendSaveTokenLifetime(): void
    {
        $this->mockApiCall(
            new Request(
                "POST",
                "/savetoken/lifetime/extend",
                [],
                '{
                    "saveToken": "st:A52CG5o5aAFiCYmDqXzggnZk3jr5Qw8jfnudA1W4r9VhgOHWns0yeBJCZXQziJLG0Jkn62DaUrOlyp4bqo9zhQ",
                    "expiresOn": "2031-01-01T00:00:00Z"
                }'
            ),
            new Response(
                200,
                [],
                '{}'
            )
        );

        $result = $this->apiClient->savetoken->extendLifetime([
            'saveToken' => 'st:A52CG5o5aAFiCYmDqXzggnZk3jr5Qw8jfnudA1W4r9VhgOHWns0yeBJCZXQziJLG0Jkn62DaUrOlyp4bqo9zhQ',
            'expiresOn' => '2031-01-01T00:00:00Z',
        ]);

        $this->assertInstanceOf(SaveToken::class, $result);
    }

    /**
     * @throws IncompatiblePlatform
     * @throws ApiException
     * @throws UnrecognizedClientException
     */
    public function testExtendSaveTokenLifetimeViaAlias(): void
    {
        $this->mockApiCall(
            new Request(
                "POST",
                "/savetoken/lifetime/extend",
                [],
                '{
                    "saveToken": "st:A52CG5o5aAFiCYmDqXzggnZk3jr5Qw8jfnudA1W4r9VhgOHWns0yeBJCZXQziJLG0Jkn62DaUrOlyp4bqo9zhQ",
                    "expiresOn": "2031-01-01T00:00:00Z"
                }'
            ),
            new Response(
                200,
                [],
                '{}'
            )
        );

        $result = $this->apiClient->saveToken->extend([
            'saveToken' => 'st:A52CG5o5aAFiCYmDqXzggnZk3jr5Qw8jfnudA1W4r9VhgOHWns0yeBJCZXQziJLG0Jkn62DaUrOlyp4bqo9zhQ',
            'expiresOn' => '2031-01-01T00:00:00Z',
        ]);

        $this->assertInstanceOf(SaveToken::class, $result);
    }

    /**
     * @throws IncompatiblePlatform
     * @throws ApiException
     * @throws UnrecognizedClientException
     */
    public function testListSaveTokens(): void
    {
        $this->mockApiCall(
            new Request(
                "POST",
                "/savetoken/list",
                [],
                '{
                    "templateName": "poster_p",
                    "page": 1
                }'
            ),
            new Response(
                200,
                [],
                '[
                    {
                        "saveToken": "st:A52CG5o5aAFiCYmDqXzggnZk3jr5Qw8jfnudA1W4r9VhgOHWns0yeBJCZXQziJLG0Jkn62DaUrOlyp4bqo9zhQ",
                        "createdOn": "2022-01-19T16:31:16.220656Z",
                        "expiresOn": "2031-01-01T00:00:00Z",
                        "templateName": "poster_p",
                        "shopBasketId": "basket_123"
                    },
                    {
                        "saveToken": "st:B63DH6p6bBFiCYmDqXzggnZk3jr5Qw8jfnudA1W4r9VhgOHWns0yeBJCZXQziJLG0Jkn62DaUrOlyp4bqo9zhQ",
                        "createdOn": "2022-01-20T12:00:00.000000Z",
                        "expiresOn": null,
                        "templateName": "poster_p",
                        "shopBasketId": null
                    }
                ]'
            )
        );

        $result = $this->apiClient->savetoken->list([
            'templateName' => 'poster_p',
            'page' => 1,
        ]);

        $this->assertInstanceOf(SaveToken::class, $result);
        $this->assertIsArray($result->getResult());
        $this->assertCount(2, $result->getResult());
        $this->assertEquals('st:A52CG5o5aAFiCYmDqXzggnZk3jr5Qw8jfnudA1W4r9VhgOHWns0yeBJCZXQziJLG0Jkn62DaUrOlyp4bqo9zhQ', $result->getResult()[0]['saveToken']);
        $this->assertEquals('2031-01-01T00:00:00Z', $result->getResult()[0]['expiresOn']);
        $this->assertNull($result->getResult()[1]['expiresOn']);
    }

    /**
     * @throws IncompatiblePlatform
     * @throws ApiException
     * @throws UnrecognizedClientException
     */
    public function testListSaveTokensWithEmptyPayload(): void
    {
        $this->mockApiCall(
            new Request(
                "POST",
                "/savetoken/list",
                [],
                '{}'
            ),
            new Response(
                200,
                [],
                '[]'
            )
        );

        $result = $this->apiClient->savetoken->list([]);

        $this->assertInstanceOf(SaveToken::class, $result);
        $this->assertIsArray($result->getResult());
        $this->assertEmpty($result->getResult());
    }

    /**
     * @throws IncompatiblePlatform
     * @throws ApiException
     * @throws UnrecognizedClientException
     */
    public function testDeleteSaveToken(): void
    {
        $this->mockApiCall(
            new Request(
                "POST",
                "/saveToken/delete",
                [],
                '{
                    "saveToken": "st:A52CG5o5aAFiCYmDqXzggnZk3jr5Qw8jfnudA1W4r9VhgOHWns0yeBJCZXQziJLG0Jkn62DaUrOlyp4bqo9zhQ"
                }'
            ),
            new Response(
                200,
                [],
                '{}'
            )
        );

        $result = $this->apiClient->savetoken->delete([
            'saveToken' => 'st:A52CG5o5aAFiCYmDqXzggnZk3jr5Qw8jfnudA1W4r9VhgOHWns0yeBJCZXQziJLG0Jkn62DaUrOlyp4bqo9zhQ',
        ]);

        $this->assertInstanceOf(SaveToken::class, $result);
    }

    /**
     * @throws IncompatiblePlatform
     * @throws ApiException
     * @throws UnrecognizedClientException
     */
    public function testResourceExtendLifetimeMethod(): void
    {
        $this->mockApiCall(
            new Request(
                "POST",
                "/savetoken/lifetime/extend",
                [],
                '{
                    "saveToken": "st:A52CG5o5aAFiCYmDqXzggnZk3jr5Qw8jfnudA1W4r9VhgOHWns0yeBJCZXQziJLG0Jkn62DaUrOlyp4bqo9zhQ",
                    "expiresOn": "2031-01-01T00:00:00Z"
                }'
            ),
            new Response(
                200,
                [],
                '{}'
            )
        );

        $tokenResource = new SaveToken($this->apiClient);
        $tokenResource->saveToken = 'st:A52CG5o5aAFiCYmDqXzggnZk3jr5Qw8jfnudA1W4r9VhgOHWns0yeBJCZXQziJLG0Jkn62DaUrOlyp4bqo9zhQ';

        $extendedResult = $tokenResource->extendLifetime('2031-01-01T00:00:00Z');
        $this->assertInstanceOf(SaveToken::class, $extendedResult);
    }

    /**
     * @throws IncompatiblePlatform
     * @throws ApiException
     * @throws UnrecognizedClientException
     */
    public function testResourceDeleteMethod(): void
    {
        $this->mockApiCall(
            new Request(
                "POST",
                "/saveToken/delete",
                [],
                '{
                    "saveToken": "st:A52CG5o5aAFiCYmDqXzggnZk3jr5Qw8jfnudA1W4r9VhgOHWns0yeBJCZXQziJLG0Jkn62DaUrOlyp4bqo9zhQ"
                }'
            ),
            new Response(
                200,
                [],
                '{}'
            )
        );

        $tokenResource = new SaveToken($this->apiClient);
        $tokenResource->saveToken = 'st:A52CG5o5aAFiCYmDqXzggnZk3jr5Qw8jfnudA1W4r9VhgOHWns0yeBJCZXQziJLG0Jkn62DaUrOlyp4bqo9zhQ';

        $deletedResult = $tokenResource->delete();
        $this->assertInstanceOf(SaveToken::class, $deletedResult);
    }
}
