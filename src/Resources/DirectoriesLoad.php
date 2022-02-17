<?php

namespace Printess\Api\Resources;

use Printess\Api\Exceptions\ApiException;

class DirectoriesLoad extends BaseResource
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
     * @param array $options
     * @param array $filters
     *
     * @return self
     * @throws ApiException
     */
    public function create(array $options = [], array $filters = []): self
    {
        return $this->client->directories->load($this->withPresetOptions($options), $filters);
    }
}
