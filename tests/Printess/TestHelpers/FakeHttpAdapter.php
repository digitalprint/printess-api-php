<?php

namespace Tests\Printess\TestHelpers;

use Printess\Api\HttpAdapter\PrintessHttpAdapterInterface;
use Psr\Http\Message\ResponseInterface;
use stdClass;

class FakeHttpAdapter implements PrintessHttpAdapterInterface
{

    /**
     * @var ResponseInterface|null
     */
    private $response;

    /**
     * @var string
     */
    private $usedMethod;

    /**
     * @var string
     */
    private $usedUrl;

    /**
     * @var array
     */
    private $usedHeaders;

    /**
     * @var string
     */
    private $usedBody;

    /**
     * FakeHttpAdapter constructor.
     * @param $response
     */
    public function __construct($response)
    {
        $this->response = $response;
    }

    /**
     * @param $httpMethod
     * @param $url
     * @param $headers
     * @param $httpBody
     * @return stdClass
     */
    public function send($httpMethod, $url, $headers, $httpBody): stdClass
    {
        $this->usedMethod = $httpMethod;
        $this->usedUrl = $url;
        $this->usedHeaders = $headers;
        $this->usedBody = $httpBody;

        return $this->parseResponseBody($this->response);
    }

    /**
     * @return string
     */
    public function versionString(): ?string
    {
        return 'fake';
    }

    /**
     * @return string
     */
    public function getUsedMethod(): string
    {
        return $this->usedMethod;
    }

    /**
     * @return string
     */
    public function getUsedUrl(): string
    {
        return $this->usedUrl;
    }

    /**
     * @return string
     */
    public function getUsedHeaders(): array
    {
        return $this->usedHeaders;
    }

    /**
     * @return string
     */
    public function getUsedBody(): string
    {
        return $this->usedBody;
    }

    /**
     * Parse the PSR-7 Response body
     *
     * @param ResponseInterface $response
     * @return stdClass|null
     */
    private function parseResponseBody(ResponseInterface $response): ?stdClass
    {
        $body = (string) $response->getBody();

        return @json_decode($body, false);
    }
}
