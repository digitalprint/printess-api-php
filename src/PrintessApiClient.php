<?php

namespace Printess\Api;

use GuzzleHttp\ClientInterface;
use Printess\Api\Exceptions\ApiException;
use Printess\Api\Exceptions\IncompatiblePlatform;
use Printess\Api\Exceptions\UnrecognizedClientException;
use Printess\Api\HttpAdapter\PrintessHttpAdapterInterface;
use Printess\Api\HttpAdapter\PrintessHttpAdapterPickerInterface;
use stdClass;

class PrintessApiClient
{
    /**
     * Version of our client.
     */
    public const CLIENT_VERSION = "1.0.0";

    /**
     * Endpoint of the remote API.
     */
    public const API_ENDPOINT = "https://api.printess.com";

    /**
     * Version of the remote API.
     */

    /**
     * HTTP Methods
     */
    const HTTP_GET = "GET";
    const HTTP_POST = "POST";
    const HTTP_DELETE = "DELETE";
    const HTTP_PATCH = "PATCH";

    /**
     * @var PrintessHttpAdapterInterface
     */
    protected $httpClient;

    /**
     * @var string
     */
    protected $apiEndpoint = self::API_ENDPOINT;

    /**
     * RESTful Payments resource.
     *
     * @var DirectoryEndpoint
     */
    public $directories;

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * True if an OAuth access token is set as API key.
     *
     * @var bool
     */
    protected $oauthAccess;

    /**
     * @var array
     */
    protected $versionStrings = [];

    /**
     * @param ClientInterface|PrintessHttpAdapterInterface|null $httpClient
     * @param PrintessHttpAdapterPickerInterface|null $httpAdapterPicker
     * @throws IncompatiblePlatform|UnrecognizedClientException
     */
    public function __construct($httpClient = null, PrintessHttpAdapterPickerInterface $httpAdapterPicker = null)
    {
        $httpAdapterPicker = $httpAdapterPicker ?: new PrintessHttpAdapterPicker;
        $this->httpClient = $httpAdapterPicker->pickHttpAdapter($httpClient);

        $compatibilityChecker = new CompatibilityChecker;
        $compatibilityChecker->checkCompatibility();

        $this->initializeEndpoints();

        $this->addVersionString("Printess/" . self::CLIENT_VERSION);
        $this->addVersionString("PHP/" . PHP_VERSION);

        $httpClientVersionString = $this->httpClient->versionString();
        if ($httpClientVersionString) {
            $this->addVersionString($httpClientVersionString);
        }
    }

    public function initializeEndpoints(): void
    {
        $this->directories = new DirectoryEndpoint($this);
    }

    /**
     * @param string $url
     *
     * @return PrintessApiClient
     */
    public function setApiEndpoint(string $url): PrintessApiClient
    {
        $this->apiEndpoint = rtrim(trim($url), '/');

        return $this;
    }

    /**
     * @return string
     */
    public function getApiEndpoint(): string
    {
        return $this->apiEndpoint;
    }

    /**
     * @return array
     */
    public function getVersionStrings(): array
    {
        return $this->versionStrings;
    }

    /**
     * @param string $apiKey The Printess API key, starting with 'test_' or 'live_'
     *
     * @return PrintessApiClient
     * @throws ApiException
     */
    public function setApiKey(string $apiKey): PrintessApiClient
    {
        $apiKey = trim($apiKey);

        if (! preg_match('/^(live|test)_\w{30,}$/', $apiKey)) {
            throw new ApiException("Invalid API key: '$apiKey'. An API key must start with 'test_' or 'live_' and must be at least 30 characters long.");
        }

        $this->apiKey = $apiKey;
        $this->oauthAccess = false;

        return $this;
    }

    /**
     * @param string $accessToken OAuth access token, starting with 'access_'
     *
     * @return PrintessApiClient
     * @throws ApiException
     */
    public function setAccessToken(string $accessToken): PrintessApiClient
    {
        $accessToken = trim($accessToken);

        if (! preg_match('/^access_\w+$/', $accessToken)) {
            throw new ApiException("Invalid OAuth access token: '$accessToken'. An access token must start with 'access_'.");
        }

        $this->apiKey = $accessToken;
        $this->oauthAccess = true;

        return $this;
    }

    /**
     * Returns null if no API key has been set yet.
     *
     * @return bool|null
     */
    public function usesOAuth(): ?bool
    {
        return $this->oauthAccess;
    }

    /**
     * @param string $versionString
     *
     * @return PrintessApiClient
     */
    public function addVersionString(string $versionString): PrintessApiClient
    {
        $this->versionStrings[] = str_replace([" ", "\t", "\n", "\r"], '-', $versionString);

        return $this;
    }

    /**
     * Perform a http call. This method is used by the resource specific classes. Please use the $payments property to
     * perform operations on payments.
     *
     * @param string $httpMethod
     * @param string $apiMethod
     * @param string|null $httpBody
     *
     * @return stdClass
     * @throws ApiException
     *
     * @codeCoverageIgnore
     */
    public function performHttpCall(string $httpMethod, string $apiMethod, string $httpBody = null): ?stdClass
    {
        $url = $this->apiEndpoint . "/" . $apiMethod;

        return $this->performHttpCallToFullUrl($httpMethod, $url, $httpBody);
    }

    /**
     * Perform an http call to a full url. This method is used by the resource specific classes.
     *
     * @param string $httpMethod
     * @param string $url
     * @param string|null $httpBody
     *
     * @return stdClass|null
     * @throws ApiException
     *
     * @codeCoverageIgnore
     *@see $isuers
     *
     * @see $payments
     */
    public function performHttpCallToFullUrl(string $httpMethod, string $url, string $httpBody = null): ?stdClass
    {
        if (empty($this->apiKey)) {
            throw new ApiException("You have not set an API key or OAuth access token. Please use setApiKey() to set the API key.");
        }

        $userAgent = implode(' ', $this->versionStrings);

        if ($this->usesOAuth()) {
            $userAgent .= " OAuth/2.0";
        }

        $headers = [
            'Accept' => "application/json",
            'Authorization' => "Bearer $this->apiKey",
            'User-Agent' => $userAgent,
        ];

        if ($httpBody !== null) {
            $headers['Content-Type'] = "application/json";
        }

        if (function_exists("php_uname")) {
            $headers['X-Printess-Client-Info'] = php_uname();
        }

        return $this->httpClient->send($httpMethod, $url, $headers, $httpBody);
    }

    /**
     * Serialization can be used for caching. Of course doing so can be dangerous but some like to live dangerously.
     *
     * \serialize() should be called on the collections or object you want to cache.
     *
     * We don't need any property that can be set by the constructor, only properties that are set by setters.
     *
     * Note that the API key is not serialized, so you need to set the key again after unserializing if you want to do
     * more API calls.
     *
     * @deprecated
     * @return string[]
     */
    public function __sleep()
    {
        return ["apiEndpoint"];
    }

    /**
     * When unserializing a collection or a resource, this class should restore itself.
     *
     * Note that if you have set an HttpAdapter, this adapter is lost on wakeup and reset to the default one.
     *
     * @throws IncompatiblePlatform If suddenly unserialized on an incompatible platform.
     * @throws UnrecognizedClientException
     */
    public function __wakeup()
    {
        $this->__construct();
    }
}
