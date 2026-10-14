<?php

namespace Printess\Api\Resources;

use Printess\Api\Exceptions\ApiException;

class SaveToken extends BaseResource
{
    /**
     * The save token string.
     *
     * @var string
     */
    public $saveToken;

    /**
     * When the design was saved.
     *
     * @var string
     */
    public $createdOn;

    /**
     * Current expiration date. null = never expires.
     *
     * @var string|null
     */
    public $expiresOn;

    /**
     * The template the design is based on.
     *
     * @var string
     */
    public $templateName;

    /**
     * The basket id your shop attached when saving.
     *
     * @var string
     */
    public $shopBasketId;

    /**
     * Either "live" or "test". Indicates this being a test or a live (verified) job.
     *
     * @var string
     */
    public $mode;

    /**
     * @param string $expiresOn
     * @param array $filters
     *
     * @return ResourceInterface
     * @throws ApiException
     */
    public function extendLifetime(string $expiresOn, array $filters = []): ResourceInterface
    {
        return $this->client->savetoken->extendLifetime([
            'saveToken' => $this->saveToken,
            'expiresOn' => $expiresOn,
        ], $filters);
    }

    /**
     * @param array $filters
     *
     * @return ResourceInterface
     * @throws ApiException
     */
    public function delete(array $filters = []): ResourceInterface
    {
        return $this->client->savetoken->delete([
            'saveToken' => $this->saveToken,
        ], $filters);
    }
}
