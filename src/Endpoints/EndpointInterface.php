<?php

namespace Printess\Api\Endpoints;

interface EndpointInterface
{
    public const RESULT_CONTEXT_OBJECT = 'object';

    public const RESULT_CONTEXT_STATUS = 'status';

    public const RESULT_CONTEXT_SIMPLE_STATUS = 'simpleStatus';

    public const RESULT_CONTEXT_RAW = 'raw';
}
