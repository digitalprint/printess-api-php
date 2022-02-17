<?php

namespace Tests\Printess\API\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Printess\Api\Exceptions\ApiException;
use Printess\Api\Exceptions\IncompatiblePlatform;
use Printess\Api\Exceptions\UnrecognizedClientException;
use Printess\Api\Resources\DirectoriesLoad;
use Tests\Printess\TestHelpers\LinkObjectTestHelpers;

class DirectoriesEndpointTest extends BaseEndpointTest
{
    use LinkObjectTestHelpers;

    /**
     * @throws IncompatiblePlatform
     * @throws ApiException
     * @throws UnrecognizedClientException
     */
    public function testGetAllDirectories(): void
    {
        $this->mockApiCall(
            new Request(
                "POST",
                "/directories/load",
                [],
                '{}'
            ),
            new Response(
                200,
                [],
                '{
                          "id": 43,
                          "pid": null,
                          "n": "",
                          "c": [
                            {
                              "id": 46,
                              "pid": 43,
                              "n": "Designs",
                              "c": [
                                {
                                  "id": 70,
                                  "pid": 46,
                                  "n": "ls_standard",
                                  "c": []
                                },
                                {
                                  "id": 211,
                                  "pid": 46,
                                  "n": "ls_Namensmotive",
                                  "c": []
                                },
                                {
                                  "id": 212,
                                  "pid": 46,
                                  "n": "ls_smartphone",
                                  "c": []
                                }
                              ]
                            }
                          ]
                        }'
            )
        );

        $status = $this->apiClient->directories->load([]);

        $this->assertInstanceOf(DirectoriesLoad::class, $status);
        $this->assertEquals('43', $status->getResult()['id']);
    }
}
