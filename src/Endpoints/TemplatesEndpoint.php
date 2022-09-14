<?php

namespace Printess\Api\Endpoints;

use Printess\Api\Exceptions\ApiException;
use Printess\Api\Resources\DirectoriesLoad;
use Printess\Api\Resources\ResourceInterface;
use Printess\Api\Resources\BaseTemplate;

class TemplatesEndpoint extends EndpointAbstract
{
    protected $resourcePath = "templates";

    /**
     * Get the object that is used by this API endpoint.
     *
     * @param string $context
     * @return DirectoriesLoad
     */
    protected function getResourceObject(string $context = EndpointInterface::RESULT_CONTEXT_RAW): ResourceInterface
    {
        return new BaseTemplate($this->client);
    }

    /**
     * Load the user directory Tree
     *
     * @param array $data An array containing details on the directory.
     * @param array $filters
     *
     * @return BaseTemplate
     * @throws ApiException
     */
    public function loadFromUser(array $data = [], array $filters = []): ResourceInterface
    {
        $this->resourcePath = "templates/user/load";

        return $this->rest_create($data, $filters, EndpointInterface::RESULT_CONTEXT_RAW);
    }

    /**
     * Load the template details
     *
     * @param array $data An array containing details
     * @param array $filters
     *
     * @return BaseTemplate
     * @throws ApiException
     */
    public function loadDetails(array $data = [], array $filters = []): ResourceInterface
    {
        $this->resourcePath = "template/details";

        return $this->rest_create($data, $filters, EndpointInterface::RESULT_CONTEXT_RAW);
    }

    /**
     * Load the template form fields
     *
     * @param array $data An array containing the form fields
     * @param array $filters
     *
     * @return BaseTemplate
     * @throws ApiException
     */
    public function loadFormFields(array $data = [], array $filters = []): ResourceInterface
    {
        $this->resourcePath = "template/formFields";

        return $this->rest_create($data, $filters, EndpointInterface::RESULT_CONTEXT_RAW);
    }
}
