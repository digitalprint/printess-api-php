<?php

namespace Printess\Api;

class Utils
{
    public static function object_to_array($object): array
    {
        return json_decode(json_encode($object), true);
    }
}
