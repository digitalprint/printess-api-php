# Developer Guide for printess-api-php

This guide contains project-specific instructions and conventions for developing, testing, and maintaining the Printess PHP API client.

---

### Build & Environment Configuration

- **PHP Requirements**: PHP >= 7.1 with extensions `ext-curl`, `ext-json`, `ext-openssl`, and `composer/ca-bundle`.
- **Dependency Management**:
  ```bash
  composer install
  ```
- **PSR-4 Autoloading**:
  - `Printess\Api\` &rarr; `src/`
  - `Tests\Printess\Api\` &rarr; `tests/Printess/API/`
  - `Tests\` &rarr; `tests/`

---

### Testing Information

#### Test Configuration
- Configuration file: `phpunit.xml` (bootstraps `./vendor/autoload.php` and whitelists `src/` for coverage).
- Test suite directory: `tests/`.

#### Running Tests
- **Run all tests**:
  ```bash
  composer test
  # or directly:
  ./vendor/bin/phpunit
  ```
- **Run a specific test suite or test file**:
  ```bash
  ./vendor/bin/phpunit tests/Printess/API/Endpoints/
  ./vendor/bin/phpunit tests/Printess/API/Endpoints/DirectoriesEndpointTest.php
  ```

#### Adding New Tests
1. **Directory & Namespace**:
   Place test classes under `tests/Printess/API/` matching the PSR-4 namespace `Tests\Printess\API\...`.
2. **Base Classes**:
   - For endpoint tests requiring mock HTTP interactions, extend `Tests\Printess\Api\Endpoints\BaseEndpointTest`.
   - For standalone unit tests, extend `PHPUnit\Framework\TestCase`.
3. **Mocking HTTP API Calls**:
   Use `$this->mockApiCall(Request $expectedRequest, Response $response, bool $oAuthClient = false)` provided by `BaseEndpointTest`. It creates a mock Guzzle client, initializes `$this->apiClient`, and asserts that the HTTP method, URI path, query parameters, and JSON payload match the expected request.
4. **API Exception Notes**:
   Printess error responses typically return JSON with `c` (code) and `m` (message) properties (`{"c": "...", "m": "..."}`) which are consumed by `ApiException::createFromResponse()`.

#### Working Test Example

```php
<?php

namespace Tests\Printess\API;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Printess\Api\Resources\DirectoriesLoad;
use Tests\Printess\Api\Endpoints\BaseEndpointTest;

class ExampleEndpointTest extends BaseEndpointTest
{
    public function testDirectoryLoadRequest(): void
    {
        $this->mockApiCall(
            new Request(
                'POST',
                '/directories/load',
                [],
                '{}'
            ),
            new Response(
                200,
                [],
                json_encode([
                    'id' => 43,
                    'n' => 'Root',
                    'c' => []
                ])
            )
        );

        $result = $this->apiClient->directories->load([]);

        $this->assertInstanceOf(DirectoriesLoad::class, $result);
        $this->assertEquals(43, $result->getResult()['id']);
    }
}
```

---

### Code Style & Quality Tooling

- **Linter/Formatter**: `friendsofphp/php-cs-fixer` configured in `.php-cs-fixer.dist.php`.
- **Ruleset**:
  - `@PSR2` base standard.
  - Short array syntax (`['syntax' => 'short']`).
  - Alphabetically sorted imports (`ordered_imports`).
  - No unused imports (`no_unused_imports`).
  - Trailing commas in multiline arrays (`trailing_comma_in_multiline`).
  - Explicit blank line before control/return statements.
- **Formatting Command**:
  ```bash
  composer format
  ```
  *Note for modern PHP runtimes (PHP 8.2+)*: Set `PHP_CS_FIXER_IGNORE_ENV=1` if running on PHP versions newer than supported by the pinned php-cs-fixer version:
  ```bash
  PHP_CS_FIXER_IGNORE_ENV=1 ./vendor/bin/php-cs-fixer fix --allow-risky=yes
  ```

---

### Architecture & Key Conventions

- **API Client Entry Point**: `Printess\Api\PrintessApiClient` exposes endpoints as public properties (e.g. `$client->production`, `$client->directories`, `$client->templates`).
- **Endpoints**: Inherit from `Printess\Api\Endpoints\EndpointAbstract` or `CollectionEndpointAbstract` and use REST helper methods (`rest_create`, `rest_read`, `rest_list`, `rest_delete`).
- **Resource Mapping**: API response payloads are hydrated into typed resource models (e.g. `DirectoriesLoad`, `GetStatus`, `ProduceJob`) via `Printess\Api\Resources\ResourceFactory::createFromApiResult()`.
- **HTTP Adapters**: HTTP calls route through `Printess\Api\HttpAdapter\PrintessHttpAdapterInterface` (selected by `PrintessHttpAdapterPicker`, defaulting to `Guzzle6And7PrintessHttpAdapter`).
