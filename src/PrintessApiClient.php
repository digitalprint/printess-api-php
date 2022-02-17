<?php

namespace Printess\Api;

use GuzzleHttp\ClientInterface;
use Printess\Api\Endpoints\DirectoriesEndpoint;
use Printess\Api\Endpoints\ProductionEndpoint;
use Printess\Api\Endpoints\TemplatesEndpoint;
use Printess\Api\Exceptions\ApiException;
use Printess\Api\Exceptions\IncompatiblePlatform;
use Printess\Api\Exceptions\UnrecognizedClientException;
use Printess\Api\HttpAdapter\PrintessHttpAdapterInterface;
use Printess\Api\HttpAdapter\PrintessHttpAdapterPicker;
use Printess\Api\HttpAdapter\PrintessHttpAdapterPickerInterface;
use stdClass;
use Tests\Printess\API\Endpoints\TemplatesEndpointTest;

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
    public const HTTP_GET = "GET";
    public const HTTP_POST = "POST";
    public const HTTP_DELETE = "DELETE";
    public const HTTP_PATCH = "PATCH";

    /**
     * @var PrintessHttpAdapterInterface
     */
    protected $httpClient;

    /**
     * @var string
     */
    protected $apiEndpoint = self::API_ENDPOINT;

    /**
     * RESTful Production resource.
     *
     * @var ProductionEndpoint
     */
    public $production;

    /**
     * RESTful Directory resource.
     *
     * @var DirectoriesEndpoint
     */
    public $directories;

    /**
     * RESTful Template resource.
     *
     * @var TemplatesEndpoint
     */
    public $templates;

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
        $httpAdapterPicker = $httpAdapterPicker ?: new PrintessHttpAdapterPicker();
        $this->httpClient = $httpAdapterPicker->pickHttpAdapter($httpClient);

        $compatibilityChecker = new CompatibilityChecker();
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
        $this->production = new ProductionEndpoint($this);
        $this->directories = new DirectoriesEndpoint($this);
        $this->templates = new TemplatesEndpoint($this);
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

        if (! preg_match('/^[A-Za-z0-9\._-]{1024,}$/', $apiKey)) {
            throw new ApiException("Invalid API key: '$apiKey'. An API key must be at least 1024 characters long.");
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

        if (! preg_match('/^[A-Za-z0-9\._-]{1024,}$/', $accessToken)) {
            throw new ApiException("Invalid API key: '$accessToken'. An API key must be at least 1024 characters long.");
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
    public function performHttpCall(string $httpMethod, string $apiMethod, string $httpBody = null): stdClass
    {
        $url = $this->apiEndpoint . "/" . $apiMethod;

        return $this->performHttpCallToFullUrl($httpMethod, $url, $httpBody);
    }

    /**
     * Perform a http call to a full url. This method is used by the resource specific classes.
     *
     * @param string $httpMethod
     * @param string $url
     * @param string|null $httpBody
     *
     * @return stdClass
     * @throws ApiException
     *
     * @codeCoverageIgnore
     */
    public function performHttpCallToFullUrl(string $httpMethod, string $url, string $httpBody = null): stdClass
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
