<?php

namespace Printess\Api\HttpAdapter;

use Printess\Api\Exceptions\ApiException;
use stdClass;

interface PrintessHttpAdapterInterface
{
    /**
     * Send a request to the specified Printess api url.
     *
     * @param $httpMethod
     * @param $url
     * @param $headers
     * @param $httpBody
     * @return stdClass|null
     * @throws ApiException
     */
    public function send($httpMethod, $url, $headers, $httpBody): ?stdClass;

    /**
     * The version number for the underlying http client, if available.
     * @example Guzzle/6.3
     *
     * @return string|null
     */
    public function versionString(): ?string;
}
