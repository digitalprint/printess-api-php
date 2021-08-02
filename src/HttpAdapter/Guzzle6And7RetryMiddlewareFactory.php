<?php

namespace Printess\Api\HttpAdapter;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class Guzzle6And7RetryMiddlewareFactory
{
    /**
     * The maximum number of retries
     */
    public const MAX_RETRIES = 5;

    /**
     * The amount of milliseconds the delay is being increased with on each retry.
     */
    public const DELAY_INCREASE_MS = 1000;

    /**
     * @param bool $delay default to true, can be false to speed up tests
     *
     * @return callable
     */
    public function retry(bool $delay = true): callable
    {
        return Middleware::retry(
            $this->newRetryDecider(),
            $delay ? $this->getRetryDelay() : $this->getZeroRetryDelay()
        );
    }

    /**
     * Returns a method that takes the number of retries and returns the number of milliseconds
     * to wait
     *
     * @return callable
     */
    private function getRetryDelay(): callable
    {
        return static function ($numberOfRetries) {
            return static::DELAY_INCREASE_MS * $numberOfRetries;
        };
    }

    /**
     * Returns a method that returns zero milliseconds to wait
     *
     * @return callable
     */
    private function getZeroRetryDelay(): callable
    {
        return static function () {
            return 0;
        };
    }

    /**
     * @return callable
     */
    private function newRetryDecider(): callable
    {
        return static function (
            $retries,
            Request $request,
            Response $response = null,
            TransferException $exception = null
        ) {
            if ($retries >= static::MAX_RETRIES) {
                return false;
            }

            if ($exception instanceof ConnectException) {
                return true;
            }

            return false;
        };
    }
}
