<?php

namespace Printess\Api\Endpoints;

use Printess\Api\Exceptions\ApiException;
use Printess\Api\Resources\GetStatus;
use Printess\Api\Resources\ProduceJob;

class ProductionEndpoint extends EndpointAbstract
{
    protected $resourcePath = "production";

    /**
     * @var string
     */
    public const RESOURCE_ID_PREFIX = '';

    /**
     * Get the object that is used by this API endpoint.
     *
     * @return ProduceJob|GetStatus
     */
    protected function getResourceObject(bool $status = false)
    {
        if (true === $status) {
            return new GetStatus($this->client);
        }

        return new ProduceJob($this->client);
    }

    /**
     * Creates a production job in Printess.
     *
     * @param array $data An array containing details on the directory.
     * @param array $filters
     *
     * @return ProduceJob
     * @throws ApiException
     */
    public function produce(array $data = [], array $filters = []): ProduceJob
    {
        $this->resourcePath = "production/produce";

        return $this->rest_create($data, $filters);
    }

    /**
     * Creates a production job in Printess.
     *
     * @param array $data An array containing details on the directory.
     * @param array $filters
     *
     * @return GetStatus
     * @throws ApiException
     */
    public function getStatus(array $data = [], array $filters = [], bool $status = false): GetStatus
    {
        $this->resourcePath = "production/status/get";

        return $this->rest_create($data, $filters, true);
    }
}
