<?php

namespace Tests\Printess\API\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Printess\Api\Exceptions\ApiException;
use Printess\Api\Exceptions\IncompatiblePlatform;
use Printess\Api\Exceptions\UnrecognizedClientException;
use Printess\Api\Resources\BaseTemplate;
use Tests\Printess\TestHelpers\LinkObjectTestHelpers;

class TemplatesEndpointTest extends BaseEndpointTest
{
    use LinkObjectTestHelpers;

    /**
     * @throws IncompatiblePlatform
     * @throws ApiException
     * @throws UnrecognizedClientException
     */
    public function testLoadUserTemplatesByDirectoryId(): void
    {
        $this->mockApiCall(
            new Request(
                "POST",
                "/templates/user/load",
                [],
                '{ "directoryId": 211 }'
            ),
            new Response(
                200,
                [],
                '{
                          "uid": "CjdyvNMW62NmmcjWtu7nuthAfNo1",
                          "e": "beck@photofancy.com",
                          "ts": [
                            {
                              "id": 867,
                              "n": "ihre_majestaet",
                              "turl": "https://printess-prod.s3.eu-central-1.amazonaws.com/uploads/thumbnails/478191510f2762b735e3066bd443f85bfa356377/template/983055584cd7e9db2a145cbf863c0ae20e58ab2c/c3d0c7c7385b1e4d8c25657c5004e98dc95beaba.png",
                              "bg": "rgb(255,255,255)",
                              "w": true,
                              "p": true,
                              "d": true,
                              "hpv": true,
                              "hdv": true,
                              "ls": "2022-01-19T16:31:16.220656Z",
                              "lp": "2022-01-19T16:31:16.386263Z"
                            },
                            {
                              "id": 869,
                              "n": "beste_ehefrau",
                              "turl": "https://printess-prod.s3.eu-central-1.amazonaws.com/uploads/thumbnails/478191510f2762b735e3066bd443f85bfa356377/template/5e7f31b1144b80c00d5bc2474ef63090b1522386/4d5aad01507895511156c1f6cd7a72bb27a3fda2.png",
                              "bg": "rgb(255,255,255)",
                              "w": true,
                              "p": true,
                              "d": true,
                              "hpv": true,
                              "hdv": true,
                              "ls": "2022-01-19T16:27:47.444275Z",
                              "lp": "2022-01-19T16:27:47.614267Z"
                            },
                            {
                              "id": 870,
                              "n": "bester_ehemann_diamant",
                              "turl": "https://printess-prod.s3.eu-central-1.amazonaws.com/uploads/thumbnails/478191510f2762b735e3066bd443f85bfa356377/template/9112490598f18cbd312fb4e250420d3d22fa19b1/479901af1c7ddaecc7d50f1f473af2fb7fb46b8d.png",
                              "bg": "rgb(255,255,255)",
                              "w": true,
                              "p": true,
                              "d": true,
                              "hpv": true,
                              "hdv": true,
                              "ls": "2022-01-13T12:25:07.116528Z",
                              "lp": "2022-01-13T12:25:07.3146Z"
                            }
                          ]
                        }'
            )
        );

        $result = $this->apiClient->templates->loadFromUser(['directoryId' => 211]);

        $this->assertInstanceOf(BaseTemplate::class, $result);

        $this->assertEquals('CjdyvNMW62NmmcjWtu7nuthAfNo1', $result->getResult()['uid']);
        $this->assertEquals('beck@photofancy.com', $result->getResult()['e']);
        $this->assertIsArray($result->getResult()['ts']);
    }

    /**
     * @throws IncompatiblePlatform
     * @throws ApiException
     * @throws UnrecognizedClientException
     */
    public function testTemplatesDetails(): void
    {
        $this->mockApiCall(
            new Request(
                "POST",
                "/template/details",
                [],
                '{ "templateId": 872 }'
            ),
            new Response(
                200,
                [],
                '{
                          "images": [
                            {
                              "tid": 872,
                              "did": "P6gSg16dPHdGMmFoOglAD",
                              "si": 0,
                              "pi": 0,
                              "tn": 0,
                              "t": "https://printess-prod.s3.eu-central-1.amazonaws.com/uploads/thumbnails/478191510f2762b735e3066bd443f85bfa356377/template/cc7cc6b396c259a0fd8dc380fe4374f544c52e13/999fff9d088eb1765917a37d8bfce6f8eaf4d5c1.png",
                              "bg": "rgb(255,255,255)"
                            },
                            {
                              "tid": 872,
                              "did": "PK5od0Mh4cfYMIMWvqulI",
                              "si": 0,
                              "pi": 0,
                              "tn": 0,
                              "t": "https://printess-prod.s3.eu-central-1.amazonaws.com/uploads/thumbnails/478191510f2762b735e3066bd443f85bfa356377/template/cc7cc6b396c259a0fd8dc380fe4374f544c52e13/fe77dffb602d19806c4e4e05ef20a766491c6a8b.png",
                              "bg": "rgb(255,255,255)"
                            },
                            {
                              "tid": 872,
                              "did": "PyW4WYhvZfXC1dpIJ2JiI",
                              "si": 0,
                              "pi": 0,
                              "tn": 0,
                              "t": "https://printess-prod.s3.eu-central-1.amazonaws.com/uploads/thumbnails/478191510f2762b735e3066bd443f85bfa356377/template/cc7cc6b396c259a0fd8dc380fe4374f544c52e13/4578d4ae142b77796465c943d31ee0bab9c33b89.png",
                              "bg": "rgb(255,255,255)"
                            }
                          ],
                          "documentInfo": "{\"PyW4WYhvZfXC1dpIJ2JiI\":\"din_p\",\"P6gSg16dPHdGMmFoOglAD\":\"square\",\"PK5od0Mh4cfYMIMWvqulI\":\"din_l\"}",
                          "id": 872,
                          "userId": "CjdyvNMW62NmmcjWtu7nuthAfNo1",
                          "template": {
                            "id": 872,
                            "n": "beste_ehefrau_diamant",
                            "turl": "https://printess-prod.s3.eu-central-1.amazonaws.com/uploads/thumbnails/478191510f2762b735e3066bd443f85bfa356377/template/cc7cc6b396c259a0fd8dc380fe4374f544c52e13/4578d4ae142b77796465c943d31ee0bab9c33b89.png",
                            "bg": "rgb(255,255,255)",
                            "w": true,
                            "p": true,
                            "d": true,
                            "hpv": true,
                            "hdv": true,
                            "ls": "2022-01-19T16:27:17.489871Z",
                            "lp": "2022-01-19T16:27:17.73586Z"
                          }
                        }'
            )
        );

        $result = $this->apiClient->templates->loadDetails(['templateId' => 872]);

        $this->assertInstanceOf(BaseTemplate::class, $result);

        $this->assertEquals(872, $result->getResult()['id']);
        $this->assertIsArray($result->getResult()['images']);
    }



    /**
     * @throws IncompatiblePlatform
     * @throws ApiException
     * @throws UnrecognizedClientException
     */
    public function testTemplatesFormFields(): void
    {
        $this->mockApiCall(
            new Request(
                "POST",
                "/template/formFields",
                [],
                '{ "templateName": "photo_snow" }'
            ),
            new Response(
                200,
                [],
                '{
                    "formFields": [
                        {
                            "name": "DOCUMENT_SIZE",
                            "display": "Document Size",
                            "value": "381x269",
                            "visibility": "admin",
                            "isPriceRelevant": false,
                            "entries": [
                                {
                                    "key": "381x269",
                                    "label": "381x269mm",
                                    "description": null
                                }
                            ]
                        },
                        {
                            "name": "edgeLeft",
                            "display": "edgeLeft",
                            "value": "12",
                            "visibility": "admin",
                            "isPriceRelevant": false,
                            "entries": []
                        },
                        {
                            "name": "edgeRight",
                            "display": "edgeTop",
                            "value": "11.5",
                            "visibility": "admin",
                            "isPriceRelevant": false,
                            "entries": []
                        },
                        {
                            "name": "COLOR_SCHEME",
                            "display": "Farbauswahl",
                            "value": "RED",
                            "visibility": "buyer",
                            "isPriceRelevant": false,
                            "entries": [
                                {
                                    "key": "RED",
                                    "label": "rot",
                                    "description": "red"
                                },
                                {
                                    "key": "BLUE",
                                    "label": "blau",
                                    "description": "blue"
                                },
                                {
                                    "key": "GREEN",
                                    "label": "grün",
                                    "description": "green"
                                }
                            ]
                        }
                    ],
                    "documentFormFields": [
                        {
                            "documentId": "P1aVYrfyvJYpQHFW84D3F",
                            "documentName": "advk",
                            "formFields": []
                        }
                    ]
                }'
            )
        );

        $result = $this->apiClient->templates->loadFormFields(['templateName' => 'photo_snow']);

        $this->assertInstanceOf(BaseTemplate::class, $result);

        $this->assertIsArray($result->getResult()['formFields']);
    }

}
