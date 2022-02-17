<?php

namespace Printess\Api\Resources;

use Printess\Api\PrintessApiClient;

class ResourceFactory
{
    /**
     * Create resource object from Api result
     *
     * @param object $apiResult
     * @param ResourceInterface $resource
     *
     * @return ResourceInterface
     */
    public static function createFromApiResult($apiResult, ResourceInterface $resource): ResourceInterface
    {
        foreach ($apiResult as $property => $value) {
            $resource->{$property} = $value;
        }

        return $resource;
    }

    /**
     * @param PrintessApiClient $client
     * @param string $resourceClass
     * @param array $data
     * @param null $_links
     * @param null $resourceCollectionClass
     * @return mixed
     */
    public static function createBaseResourceCollection(
        PrintessApiClient $client,
        string            $resourceClass,
        array             $data,
        $_links = null,
        $resourceCollectionClass = null
    ) {
        $resourceCollectionClass = $resourceCollectionClass ?: $resourceClass . 'Collection';
        $data = $data ?: [];

        $result = new $resourceCollectionClass(count($data), $_links);
        foreach ($data as $item) {
            $result[] = static::createFromApiResult($item, new $resourceClass($client));
        }

        return $result;
    }

    /**
     * @param PrintessApiClient $client
     * @param array $input
     * @param string $resourceClass
     * @param null $_links
     * @param null $resourceCollectionClass
     * @return mixed
     */
    public static function createCursorResourceCollection(
        PrintessApiClient $client,
        array             $input,
        string            $resourceClass,
        $_links = null,
        $resourceCollectionClass = null
    ) {
        if (null === $resourceCollectionClass) {
            $resourceCollectionClass = $resourceClass.'Collection';
        }

        $data = new $resourceCollectionClass($client, count($input), $_links);
        foreach ($input as $item) {
            $data[] = static::createFromApiResult($item, new $resourceClass($client));
        }

        return $data;
    }
}
