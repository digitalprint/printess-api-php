<?php

namespace Printess\Api\Endpoints;

use Printess\Api\Exceptions\ApiException;
use Printess\Api\Resources\GetSimpleStatus;
use Printess\Api\Resources\GetStatus;
use Printess\Api\Resources\ProduceJob;
use Printess\Api\Resources\ResourceInterface;

class ProductionEndpoint extends EndpointAbstract
{
    protected $resourcePath = "production";

    /**
     * Get the object that is used by this API endpoint.
     *
     * @param string $context
     * @return ResourceInterface
     */
    protected function getResourceObject(string $context = EndpointInterface::RESULT_CONTEXT_OBJECT): ResourceInterface
    {
        if (EndpointInterface::RESULT_CONTEXT_STATUS === $context) {
            return new GetStatus($this->client);
        }

        if (EndpointInterface::RESULT_CONTEXT_SIMPLE_STATUS === $context) {
            return new GetSimpleStatus($this->client);
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
    public function produce(array $data = [], array $filters = []): ResourceInterface
    {
        $this->resourcePath = "production/produce";

        return $this->rest_create($data, $filters);
    }

    /**
     * Creates a production job in Printess.
     *
     * @param array $data An array containing details on the directory.
     * @param array $filters
     * @return GetStatus
     * @throws ApiException
     */
    public function getStatus(array $data = [], array $filters = []): ResourceInterface
    {
        $this->resourcePath = "production/status/get";

        return $this->rest_create($data, $filters, EndpointInterface::RESULT_CONTEXT_STATUS);
    }

    /**
     * Creates a production job in Printess.
     *
     * @param array $data An array containing details on the directory.
     * @param array $filters
     * @return GetSimpleStatus
     * @throws ApiException
     */
    public function getSimpleStatus(array $data = [], array $filters = []): ResourceInterface
    {
        $this->resourcePath = "production/status/pdf/get";

        return $this->rest_create($data, $filters, EndpointInterface::RESULT_CONTEXT_SIMPLE_STATUS);
    }
}
