<?php

namespace Printess\Api\HttpAdapter;

use GuzzleHttp\ClientInterface;
use Printess\Api\Exceptions\UnrecognizedClientException;

class PrintessHttpAdapterPicker implements PrintessHttpAdapterPickerInterface
{
    /**
     * @param ClientInterface|PrintessHttpAdapterInterface $httpClient
     *
     * @return PrintessHttpAdapterInterface
     * @throws UnrecognizedClientException
     */
    public function pickHttpAdapter($httpClient): PrintessHttpAdapterInterface
    {
        if (! $httpClient) {
            if ($this->guzzleIsDetected()) {
                $guzzleVersion = $this->guzzleMajorVersionNumber();

                if (in_array($guzzleVersion, [6, 7], true)) {
                    return Guzzle6And7PrintessHttpAdapter::createDefault();
                }
            }

            return new CurlPrintessHttpAdapter;
        }

        if ($httpClient instanceof PrintessHttpAdapterInterface) {
            return $httpClient;
        }

        if ($httpClient instanceof ClientInterface) {
            return new Guzzle6And7PrintessHttpAdapter($httpClient);
        }

        throw new UnrecognizedClientException('The provided http client or adapter was not recognized.');
    }

    /**
     * @return bool
     */
    private function guzzleIsDetected(): bool
    {
        return interface_exists(ClientInterface::class);
    }

    /**
     * @return int|null
     */
    private function guzzleMajorVersionNumber(): ?int
    {
        // Guzzle 7
        if (defined('\GuzzleHttp\ClientInterface::MAJOR_VERSION')) {
            return ClientInterface::MAJOR_VERSION;
        }

        // Before Guzzle 7
        if (defined('\GuzzleHttp\ClientInterface::VERSION')) {
            return (int) ClientInterface::VERSION[0];
        }

        return null;
    }
}
