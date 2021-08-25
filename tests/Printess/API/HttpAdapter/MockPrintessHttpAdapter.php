<?php

namespace Tests\Printess\API\HttpAdapter;

use Printess\Api\HttpAdapter\PrintessHttpAdapterInterface;
use stdClass;

class MockPrintessHttpAdapter implements PrintessHttpAdapterInterface
{
    /**
     * @inheritDoc
     */
    public function send($httpMethod, $url, $headers, $httpBody): ?stdClass
    {
        return (object) ['foo' => 'bar'];
    }

    /**
     * @inheritDoc
     */
    public function versionString(): ?string
    {
        return 'mock-client/1.0';
    }
}
