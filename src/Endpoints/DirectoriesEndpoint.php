<?php

namespace Printess\Api\Endpoints;

use Printess\Api\Exceptions\ApiException;
use Printess\Api\Resources\DirectoriesLoad;
use Printess\Api\Resources\ResourceInterface;

class DirectoriesEndpoint extends EndpointAbstract
{
    protected $resourcePath = "directories";

    /**
     * Get the object that is used by this API endpoint.
     *
     * @param string $context
     * @return DirectoriesLoad
     */
    protected function getResourceObject(string $context = EndpointInterface::RESULT_CONTEXT_RAW): ResourceInterface
    {
        return new DirectoriesLoad($this->client);
    }

    /**
     * Load the user directory Tree
     *
     * @param array $data An array containing details on the directory.
     * @param array $filters
     *
     * @return DirectoriesLoad
     * @throws ApiException
     */
    public function load(array $data = [], array $filters = []): ResourceInterface
    {
        $this->resourcePath = "directories/load";

        return $this->rest_create($data, $filters, EndpointInterface::RESULT_CONTEXT_RAW);
    }
}
