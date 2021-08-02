<?php

namespace Printess\Api\HttpAdapter;

use GuzzleHttp\ClientInterface;

interface PrintessHttpAdapterPickerInterface
{
    /**
     * @param ClientInterface|PrintessHttpAdapterInterface $httpClient
     *
     * @return PrintessHttpAdapterInterface
     */
    public function pickHttpAdapter($httpClient): PrintessHttpAdapterInterface;
}
