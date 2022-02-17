<?php

namespace Printess\Api\Endpoints;

use InvalidArgumentException;
use Printess\Api\Exceptions\ApiException;
use Printess\Api\PrintessApiClient;
use Printess\Api\Resources\BaseCollectionInterface;
use Printess\Api\Resources\ResourceFactory;
use Printess\Api\Resources\ResourceInterface;
use Printess\Api\Utils;

abstract class EndpointAbstract implements EndpointInterface
{
    public const REST_CREATE = PrintessApiClient::HTTP_POST;
    public const REST_UPDATE = PrintessApiClient::HTTP_PATCH;
    public const REST_READ = PrintessApiClient::HTTP_GET;
    public const REST_LIST = PrintessApiClient::HTTP_GET;
    public const REST_DELETE = PrintessApiClient::HTTP_DELETE;

    /**
     * @var PrintessApiClient
     */
    protected $client;

    /**
     * @var string
     */
    protected $resourcePath;

    /**
     * @var string|null
     */
    protected $parentId;

    /**
     * @param PrintessApiClient $api
     */
    public function __construct(PrintessApiClient $api)
    {
        $this->client = $api;
    }

    /**
     * @param array $filters
     * @return string
     */
    protected function buildQueryString(array $filters): string
    {
        if (empty($filters)) {
            return "";
        }

        foreach ($filters as $key => $value) {
            if ($value === true) {
                $filters[$key] = "true";
            }

            if ($value === false) {
                $filters[$key] = "false";
            }
        }

        return "?" . http_build_query($filters);
    }

    /**
     * @param array $body
     * @param array $filters
     * @param string $context
     * @return ResourceInterface
     * @throws ApiException
     */
    protected function rest_create(array $body, array $filters, string $context = EndpointInterface::RESULT_CONTEXT_OBJECT): ResourceInterface
    {
        $result = $this->client->performHttpCall(
            self::REST_CREATE,
            $this->getResourcePath() . $this->buildQueryString($filters),
            $this->parseRequestBody($body)
        );

        if (EndpointInterface::RESULT_CONTEXT_RAW === $context) {
            $resourceObject = $this->getResourceObject($context);
            $resourceObject->setResult(Utils::object_to_array($result));

            return $resourceObject;
        }

        return ResourceFactory::createFromApiResult($result, $this->getResourceObject($context));
    }

    /**
     * Sends a PATCH request to a single Printess API object.
     *
     * @param string $id
     * @param array $body
     *
     * @return ResourceInterface
     * @throws ApiException
     */
    protected function rest_update(string $id, array $body = []): ?ResourceInterface
    {
        if (empty($id)) {
            throw new ApiException("Invalid resource id.");
        }

        $id = urlencode($id);
        $result = $this->client->performHttpCall(
            self::REST_UPDATE,
            "{$this->getResourcePath()}/$id",
            $this->parseRequestBody($body)
        );

        if ($result === null) {
            return null;
        }

        return ResourceFactory::createFromApiResult($result, $this->getResourceObject());
    }

    /**
     * Retrieves a single object from the REST API.
     *
     * @param string $id Id of the object to retrieve.
     * @param array $filters
     * @return ResourceInterface
     * @throws ApiException
     */
    protected function rest_read(string $id, array $filters): ResourceInterface
    {
        if (empty($id)) {
            throw new ApiException("Invalid resource id.");
        }

        $id = urlencode($id);
        $result = $this->client->performHttpCall(
            self::REST_READ,
            "{$this->getResourcePath()}/$id" . $this->buildQueryString($filters)
        );

        return ResourceFactory::createFromApiResult($result, $this->getResourceObject());
    }

    /**
     * Sends a DELETE request to a single Printess API object.
     *
     * @param string $id
     * @param array $body
     *
     * @return ResourceInterface
     * @throws ApiException
     */
    protected function rest_delete(string $id, array $body = []): ?ResourceInterface
    {
        if (empty($id)) {
            throw new ApiException("Invalid resource id.");
        }

        $id = urlencode($id);
        $result = $this->client->performHttpCall(
            self::REST_DELETE,
            "{$this->getResourcePath()}/$id",
            $this->parseRequestBody($body)
        );

        if (null === $result) {
            return null;
        }

        return ResourceFactory::createFromApiResult($result, $this->getResourceObject());
    }

    /**
     * Get a collection of objects from the REST API.
     *
     * @param string|null $from The first resource ID you want to include in your list.
     * @param int|null $limit
     * @param array $filters
     *
     * @return BaseCollectionInterface
     * @throws ApiException
     */
    protected function rest_list(string $from = null, int $limit = null, array $filters = []): BaseCollectionInterface
    {
        $filters = array_merge(["from" => $from, "limit" => $limit], $filters);

        $apiPath = $this->getResourcePath() . $this->buildQueryString($filters);

        $result = $this->client->performHttpCall(self::REST_LIST, $apiPath);

        $collection = $this->getResourceCollectionObject($result->count, $result->_links);

        foreach ($result->_embedded->{$collection->getCollectionResourceName()} as $dataResult) {
            $collection[] = ResourceFactory::createFromApiResult($dataResult, $this->getResourceObject());
        }

        return $collection;
    }

    /**
     * Get the object that is used by this API endpoint. Every API endpoint uses one type of object.
     *
     * @param string $context
     * @return ResourceInterface
     */
    abstract protected function getResourceObject(string $context = EndpointInterface::RESULT_CONTEXT_OBJECT): ResourceInterface;

    /**
     * @param string $resourcePath
     */
    public function setResourcePath(string $resourcePath): void
    {
        $this->resourcePath = strtolower($resourcePath);
    }

    /**
     * @return string
     * @throws ApiException
     */
    public function getResourcePath(): string
    {
        if (strpos($this->resourcePath, "_") !== false) {
            [$parentResource, $childResource] = explode("_", $this->resourcePath, 2);

            if (empty($this->parentId)) {
                throw new ApiException("Subresource '$this->resourcePath' used without parent '$parentResource' ID.");
            }

            return "$parentResource/$this->parentId/$childResource";
        }

        return $this->resourcePath;
    }

    /**
     * @param array $body
     * @return null|string
     * @throws ApiException
     */
    protected function parseRequestBody(array $body): ?string
    {
        if (empty($body)) {
            return null;
        }

        try {
            $encoded = @json_encode($body);
        } catch (InvalidArgumentException $e) {
            throw new ApiException("Error encoding parameters into JSON: '".$e->getMessage()."'.");
        }

        return $encoded;
    }
}
