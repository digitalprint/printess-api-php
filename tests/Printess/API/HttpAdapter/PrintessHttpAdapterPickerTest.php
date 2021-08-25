<?php

namespace Tests\Printess\API\HttpAdapter;

use GuzzleHttp\Client as GuzzleClient;
use Printess\Api\Exceptions\UnrecognizedClientException;
use Printess\Api\HttpAdapter\Guzzle6And7PrintessHttpAdapter;
use Printess\Api\HttpAdapter\PrintessHttpAdapterPicker;
use PHPUnit\Framework\TestCase;

class PrintessHttpAdapterPickerTest extends TestCase
{
    /** @test
     * @throws UnrecognizedClientException
     */
    public function createsAGuzzleAdapterIfNullIsPassedAndGuzzleIsDetected(): void
    {
        $picker = new PrintessHttpAdapterPicker;

        $adapter = $picker->pickHttpAdapter(null);

        $this->assertInstanceOf(Guzzle6And7PrintessHttpAdapter::class, $adapter);
    }

    /** @test
     * @throws UnrecognizedClientException
     */
    public function returnsTheAdapterThatWasPassedIn(): void
    {
        $picker = new PrintessHttpAdapterPicker;
        $mockAdapter = new MockPrintessHttpAdapter;

        $adapter = $picker->pickHttpAdapter($mockAdapter);

        $this->assertInstanceOf(MockPrintessHttpAdapter::class, $adapter);
        $this->assertEquals($mockAdapter, $adapter);
    }

    /** @test
     * @throws UnrecognizedClientException
     */
    public function wrapsAGuzzleClientIntoAnAdapter(): void
    {
        $picker = new PrintessHttpAdapterPicker;
        $guzzleClient = new GuzzleClient;

        $adapter = $picker->pickHttpAdapter($guzzleClient);

        $this->assertInstanceOf(Guzzle6And7PrintessHttpAdapter::class, $adapter);
    }

    /** @test
     * @throws UnrecognizedClientException
     */
    public function throwsAnExceptionWhenReceivingAnUnrecognizedClient(): void
    {
        $this->expectExceptionObject(new UnrecognizedClientException('The provided http client or adapter was not recognized'));
        $picker = new PrintessHttpAdapterPicker;
        $unsupportedClient = (object) ['foo' => 'bar'];

        $picker->pickHttpAdapter($unsupportedClient);
    }
}
