<?php

namespace Printess\Api\Resources;

use Printess\Api\Exceptions\ApiException;

class GetStatus extends BaseResource
{
    /**
     * @var string
     */
    public $resource;

    /**
     * Either "live" or "test". Indicates this being a test or a live (verified) job.
     *
     * @var string
     */
    public $mode;

    /**
     * Id of the job.
     *
     * @var string
     */
    public $jobId;

    /**
     * orderId of the job.
     *
     * @var string
     */
    public $result;

    /**
     * @param array $options
     * @param array $filters
     *
     * @return self
     * @throws ApiException
     */
    public function create(array $options = [], array $filters = []): self
    {
        return $this->client->production->getStatus($this->withPresetOptions($options), $filters);
    }

    /**
     * When accessed by oAuth we want to pass the testmode by default
     *
     * @return array
     */
    private function getPresetOptions(): array
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
    private function withPresetOptions(array $options): array
    {
        return array_merge($this->getPresetOptions(), $options);
    }
}
