<?php

namespace Printess\Api\Resources;

use ArrayObject;
use stdClass;

abstract class BaseCollection extends ArrayObject implements BaseCollectionInterface
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return string|null
     */
    abstract public function getCollectionResourceName(): ?string;
}
