<?php

namespace Tests\Printess\API\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Printess\Api\Exceptions\ApiException;
use Printess\Api\Exceptions\IncompatiblePlatform;
use Printess\Api\Exceptions\UnrecognizedClientException;
use Printess\Api\Resources\GetSimpleStatus;
use Printess\Api\Resources\GetStatus;
use Printess\Api\Resources\ProduceJob;
use Tests\Printess\TestHelpers\LinkObjectTestHelpers;

class ProductionEndpointTest extends BaseEndpointTest
{
    use LinkObjectTestHelpers;

    /**
     * @throws IncompatiblePlatform
     * @throws ApiException
     * @throws UnrecognizedClientException
     */
    public function testProduceJobViaTemplateName(): void
    {
        $this->mockApiCall(
            new Request(
                "POST",
                "/production/produce",
                [],
                '{
                  "templateName": "st:A52CG5o5aAFiCYmDqXzggnZk3jr5Qw8jfnudA1W4r9VhgOHWns0yeBJCZXQziJLG0Jkn62DaUrOlyp4bqo9zhQ",
                  "outputSettings": {
                    "dpi": 150
                  },
                  "outputFiles": [
                    {
                      "documentName": "poster_p"
                    }
                  ],  
                  "origin": "flip"
                }'
            ),
            new Response(
                200,
                [],
                '{
                    "jobId": "478191510f2762b735e3066bd443f85bfa356377_20210825T105914720_5ef63a98-fcf6-45be-b160-6e6504b9c242",
                    "orderId": "5f47b789-4482-4b20-a853-a4c9c462173d"
                }'
            )
        );

        $job = $this->apiClient->production->produce([
            'templateName' => 'st:A52CG5o5aAFiCYmDqXzggnZk3jr5Qw8jfnudA1W4r9VhgOHWns0yeBJCZXQziJLG0Jkn62DaUrOlyp4bqo9zhQ',
            'outputSettings' => ['dpi' => 150],
            'outputFiles' => [
                [ 'documentName' => 'poster_p' ],
            ],
            'origin' => 'flip',
        ]);

        $this->assertInstanceOf(ProduceJob::class, $job);
        $this->assertEquals('478191510f2762b735e3066bd443f85bfa356377_20210825T105914720_5ef63a98-fcf6-45be-b160-6e6504b9c242', $job->jobId);
        $this->assertEquals('5f47b789-4482-4b20-a853-a4c9c462173d', $job->orderId);
    }

    /**
     * @throws IncompatiblePlatform
     * @throws ApiException
     * @throws UnrecognizedClientException
     */
    public function testProduceJobViaTemplateNameWithFormFields(): void
    {
        $this->mockApiCall(
            new Request(
                "POST",
                "/production/produce",
                [],
                '{
                    "templateName": "poster_p",
                    "origin": "flip",
                    "meta": "{\"name\":\"Lutz Bickers\"}",
                    "outputSettings": {
                        "dpi": 300
                    },
                    "vdp": {
                        "form": {
                            "text1": "Simon & Garfunkel",
                            "text2": "Bridge over Troubled Water",
                            "image1": "https://images.unsplash.com/photo-1623680904963-5580d963e18e?ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=2134&q=80",
                            "backgroundColor": "Dunkelblau",
                            "remote": "ON",
                            "DOCUMENT_SIZE": "29.7x42_118315h"
                        }
                    }    
                }'
            ),
            new Response(
                200,
                [],
                '{
                    "jobId": "478191510f2762b735e3066bd443f85bfa356377_20210825T105914720_5ef63a98-fcf6-45be-b160-6e6504b9c242",
                    "orderId": "5f47b789-4482-4b20-a853-a4c9c462173d"
                }'
            )
        );

        $job = $this->apiClient->production->produce([
            'templateName' => 'poster_p',
            'origin' => 'flip',
            'meta' => '{"name":"Lutz Bickers"}',
            'outputSettings' => [
                'dpi' => 300,
            ],
            'vdp' => [
                'form' => [
                    'text1' => 'Simon & Garfunkel',
                    'text2' => 'Bridge over Troubled Water',
                    'image1' => 'https://images.unsplash.com/photo-1623680904963-5580d963e18e?ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=2134&q=80',
                    'backgroundColor' => 'Dunkelblau',
                    'remote' => 'ON',
                    'DOCUMENT_SIZE' => '29.7x42_118315h',
                ],
            ],
        ]);

        $this->assertInstanceOf(ProduceJob::class, $job);
        $this->assertEquals('478191510f2762b735e3066bd443f85bfa356377_20210825T105914720_5ef63a98-fcf6-45be-b160-6e6504b9c242', $job->jobId);
        $this->assertEquals('5f47b789-4482-4b20-a853-a4c9c462173d', $job->orderId);
    }


    /**
     * @throws IncompatiblePlatform
     * @throws ApiException
     * @throws UnrecognizedClientException
     */
    public function testProduceMergeProductTemplateWithDesignTemplate(): void
    {
        $this->mockApiCall(
            new Request(
                "POST",
                "/production/produce",
                [],
                '{
                    "templateName": "poster_p",
                    "origin": "flip",
                    "outputFiles": [
                        {
                            "outputSettings": {
                                "dpi": 300
                            },
                            "vdp": {
                                "form": {
                                    "text1": "Simon & Garfunkel",
                                    "text2": "Bridge over Troubled Water",
                                    "image1": "https://images.unsplash.com/photo-1623680904963-5580d963e18e?ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=2134&q=80",
                                    "backgroundColor": "Dunkelblau",
                                    "remote": "ON",
                                    "DOCUMENT_SIZE": "29.7x42_118315h"
                                }
                            },
                            "documentName": "preview",
                            "mergeTemplates": [
                                {
                                    "templateName": "song_cover_bright",
                                    "documentName": "din_p"
                                }
                            ]
                        }
                    ]
                }'
            ),
            new Response(
                200,
                [],
                '{
                    "jobId": "478191510f2762b735e3066bd443f85bfa356377_20210825T105914720_5ef63a98-fcf6-45be-b160-6e6504b9c242",
                    "orderId": "5f47b789-4482-4b20-a853-a4c9c462173d"
                }'
            )
        );

        $job = $this->apiClient->production->produce([
            'templateName' => 'poster_p',
            'origin' => 'flip',
            'outputFiles' => [
                [
                    'outputSettings' => [
                        'dpi' => 300,
                    ],
                    'vdp' => [
                        'form' => [
                            'text1' => 'Simon & Garfunkel',
                            'text2' => 'Bridge over Troubled Water',
                            'image1' => 'https://images.unsplash.com/photo-1623680904963-5580d963e18e?ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=2134&q=80',
                            'backgroundColor' => 'Dunkelblau',
                            'remote' => 'ON',
                            'DOCUMENT_SIZE' => '29.7x42_118315h',
                        ],
                    ],
                    'documentName' => 'preview',
                    'mergeTemplates' => [
                        [
                            'templateName' => 'song_cover_bright',
                            'documentName' => 'din_p',
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertInstanceOf(ProduceJob::class, $job);
        $this->assertEquals('478191510f2762b735e3066bd443f85bfa356377_20210825T105914720_5ef63a98-fcf6-45be-b160-6e6504b9c242', $job->jobId);
        $this->assertEquals('5f47b789-4482-4b20-a853-a4c9c462173d', $job->orderId);
    }

    /**
     * @throws IncompatiblePlatform
     * @throws ApiException
     * @throws UnrecognizedClientException
     */
    public function testGetStatusOfEnqueuedJob(): void
    {
        $this->mockApiCall(
            new Request(
                "POST",
                "/production/status/get",
                [],
                '{
                  "jobId": "478191510f2762b735e3066bd443f85bfa356377_20210825T105914720_5ef63a98-fcf6-45be-b160-6e6504b9c242"
                }'
            ),
            new Response(
                200,
                [],
                '{
                    "jobId": "478191510f2762b735e3066bd443f85bfa356377_20210825T105914720_5ef63a98-fcf6-45be-b160-6e6504b9c242",
                    "isFinalStatus": true,
                    "isSuccess": true,
                    "enqueuedOn": "2021-08-25T11:48:39.5037687Z",
                    "processingOn": "2021-08-25T11:48:39.5235293Z",
                    "finishedOn": "2021-08-25T11:48:40.0226477Z",
                    "failedOn": null,
                    "errorDetails": null,
                    "result": {
                        "s": {
                            "t": 0,
                            "p": 63.6652,
                            "c": 215.3604,
                            "o": 0,
                            "d": 198.59
                        },
                        "r": {
                            "preview": "https://printess-prod.s3.eu-central-1.amazonaws.com/output/478191510f2762b735e3066bd443f85bfa356377_20210825T114839396_091d0653-5ca8-43c1-ab1a-f65d11389107_1.pdf"
                        },
                        "d": {
                            "preview": "478191510f2762b735e3066bd443f85bfa356377_20210825T114839396_091d0653-5ca8-43c1-ab1a-f65d11389107_1.pdf"
                        },
                        "p": null,
                        "meta": null,
                        "zip": null
                    }
                }'
            )
        );

        $status = $this->apiClient->production->getStatus([
            "jobId" => "478191510f2762b735e3066bd443f85bfa356377_20210825T105914720_5ef63a98-fcf6-45be-b160-6e6504b9c242",
        ]);

        $this->assertInstanceOf(GetStatus::class, $status);
        $this->assertEquals('478191510f2762b735e3066bd443f85bfa356377_20210825T105914720_5ef63a98-fcf6-45be-b160-6e6504b9c242', $status->jobId);
    }

    /**
     * @throws IncompatiblePlatform
     * @throws ApiException
     * @throws UnrecognizedClientException
     */
    public function testGetSimpleStatusOfEnqueuedJob(): void
    {
        $this->mockApiCall(
            new Request(
                "POST",
                "/production/status/pdf/get",
                [],
                '{
                  "jobId": "478191510f2762b735e3066bd443f85bfa356377_20230116T132507330_8f863082-9eff-4322-9b86-564318988460"
                }'
            ),
            new Response(
                200,
                [],
                '{
                    "isFinalStatus": true,
                    "isError": false,
                    "errorDetails": null,
                    "pdfs": [
                        {
                            "documentName": "print",
                            "url": "https://printess-prod.s3.eu-central-1.amazonaws.com/output/478191510f2762b735e3066bd443f85bfa356377_20230116T132507330_8f863082-9eff-4322-9b86-564318988460_1.pdf"
                        }
                    ]
                }'
            )
        );

        $status = $this->apiClient->production->getSimpleStatus([
            "jobId" => "478191510f2762b735e3066bd443f85bfa356377_20230116T132507330_8f863082-9eff-4322-9b86-564318988460",
        ]);

        $this->assertInstanceOf(GetSimpleStatus::class, $status);
        $this->assertCount(1, $status->pdfs);
    }
}
