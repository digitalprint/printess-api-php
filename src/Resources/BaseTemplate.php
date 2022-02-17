<?php

namespace Printess\Api\Resources;

use Printess\Api\Exceptions\ApiException;

class BaseTemplate extends BaseResource
{
    /**
     * Either "live" or "test". Indicates this being a test or a live (verified) job.
     *
     * @var string
     */
    public $mode;
}
