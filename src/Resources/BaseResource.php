<?php

namespace Printess\Api\Resources;

use Printess\Api\PrintessApiClient;

abstract class BaseResource implements ResourceInterface
{
    /**
     * @var PrintessApiClient
     */
    protected $client;

    /**
     * @var array
     */
    protected $rawResult;

    /**
     * @param PrintessApiClient $client
     */
    public function __construct(PrintessApiClient $client)
    {
        $this->client = $client;
    }

    /**
     * @return array
     */
    public function getResult(): array
    {
        return $this->rawResult;
    }

    /**
     * @param array $result
     */
    public function setResult(array $result): void
    {
        $this->rawResult = $result;
    }

    /**
     * When accessed by oAuth we want to pass the testmode by default
     *
     * @return array
     */
    protected function getPresetOptions(): array
    {
        $options = [];
        if ($this->client->usesOAuth()) {
            $options["testmode"] = $this->mode === "test";
        }

        return $options;
    }

    /**
     * Apply the preset options.
     *
     * @param array $options
     * @return array
     */
    protected function withPresetOptions(array $options): array
    {
        return array_merge($this->getPresetOptions(), $options);
    }
}
