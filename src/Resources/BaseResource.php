<?php

namespace Printess\Api\Resources;

use Printess\Api\PrintessApiClient;

abstract class BaseResource
{
    /**
     * @var PrintessApiClient
     */
    protected $client;

    /**
     * @param PrintessApiClient $client
     */
    public function __construct(PrintessApiClient $client)
    {
        $this->client = $client;
    }
}
