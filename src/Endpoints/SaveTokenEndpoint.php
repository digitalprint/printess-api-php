<?php

namespace Printess\Api\Endpoints;

use Printess\Api\Exceptions\ApiException;
use Printess\Api\Resources\ResourceInterface;
use Printess\Api\Resources\SaveToken;

class SaveTokenEndpoint extends EndpointAbstract
{
    protected $resourcePath = "savetoken";

    /**
     * Get the object that is used by this API endpoint.
     *
     * @param string $context
     * @return ResourceInterface
     */
    protected function getResourceObject(string $context = EndpointInterface::RESULT_CONTEXT_RAW): ResourceInterface
    {
        return new SaveToken($this->client);
    }

    /**
     * Extend save token lifetime.
     *
     * @param array $data
     * @param array $filters
     *
     * @return ResourceInterface
     * @throws ApiException
     */
    public function extendLifetime(array $data = [], array $filters = []): ResourceInterface
    {
        $this->resourcePath = "savetoken/lifetime/extend";

        return $this->rest_create($data, $filters, EndpointInterface::RESULT_CONTEXT_RAW);
    }

    /**
     * Alias for extendLifetime.
     *
     * @param array $data
     * @param array $filters
     *
     * @return ResourceInterface
     * @throws ApiException
     */
    public function extend(array $data = [], array $filters = []): ResourceInterface
    {
        return $this->extendLifetime($data, $filters);
    }

    /**
     * List save tokens.
     *
     * @param array $data
     * @param array $filters
     *
     * @return ResourceInterface
     * @throws ApiException
     */
    public function list(array $data = [], array $filters = []): ResourceInterface
    {
        $this->resourcePath = "savetoken/list";

        return $this->rest_create($data, $filters, EndpointInterface::RESULT_CONTEXT_RAW);
    }

    /**
     * Delete save token.
     *
     * @param array $data
     * @param array $filters
     *
     * @return ResourceInterface
     * @throws ApiException
     */
    public function delete(array $data = [], array $filters = []): ResourceInterface
    {
        $this->resourcePath = "saveToken/delete";

        return $this->rest_create($data, $filters, EndpointInterface::RESULT_CONTEXT_RAW);
    }
}
