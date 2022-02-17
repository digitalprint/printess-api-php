<?php

namespace Printess\Api\Resources;

interface ResourceInterface
{
    public function setResult(array $result): void;

    public function getResult(): array;
}
