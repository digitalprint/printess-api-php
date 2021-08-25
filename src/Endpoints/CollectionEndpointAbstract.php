<?php

namespace Printess\Api\Endpoints;

use Printess\Api\Resources\BaseCollection;
use stdClass;

abstract class CollectionEndpointAbstract extends EndpointAbstract
{
    /**
     * Get the collection object that is used by this API endpoint. Every API endpoint uses one type of collection object.
     *
     * @param int $count
     * @param stdClass $_links
     *
     * @return BaseCollection
     */
    abstract protected function getResourceCollectionObject(int $count, stdClass $_links);
}
