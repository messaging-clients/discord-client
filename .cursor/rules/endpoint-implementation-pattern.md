# Discord API Endpoint Implementation Pattern

This document outlines the standard pattern for implementing new Discord API endpoints in the DiscordClient library. Follow this pattern consistently to ensure code quality, maintainability, and testability.

## Overview

When adding support for a new Discord API endpoint, you must create or modify files in three main areas:
1. **Source Code** (`src/DiscordClient.php`) - Add the endpoint method
2. **Tests** (`tests/Integration/DiscordClient/`) - Create integration tests
3. **Examples** (`examples/`) - Create usage examples

Additionally, if the endpoint requires new constants or enums, create them in `src/Constants/`.

## 1. DiscordClient.php Modifications

### Method Structure

Add a new public method to the `DiscordClient` class following this pattern:

```php
/**
 * [Description of what the endpoint does]
 *
 * [Additional context, e.g., "When used with a bot token, this returns..."]
 *
 * @param [type] $param [description]
 * @return HttpClientResponse
 * @throws MissingTokenException|MissingAuthorizationTypeException
 */
public function methodName([parameters]): HttpClientResponse
{
    // 1. Prepare the request
    $this->client
        ->prepareRequest('HTTP_METHOD', $this->baseUri . '/endpoint/path')
        ->setHeader('Content-Type', 'application/json');

    // 2. Add authorization headers
    $this->withAuthorization();

    // 3. Set request body (for POST, PUT, PATCH requests)
    if (needsBody) {
        $this->client->getRequest()->setJson($data);
        // OR for form-encoded data:
        // $this->client->getRequest()->setUrlEncodedData($data);
    }

    // 4. Execute and return
    return $this->client->execute();
}
```

### Key Points:

- **HTTP Method**: Use the appropriate HTTP method (GET, POST, PUT, DELETE, PATCH)
- **Base URI**: Always use `$this->baseUri` (which is `https://discord.com/api/v10`) + the endpoint path
- **Content-Type**: Set to `'application/json'` for JSON endpoints
- **Authorization**: Always call `$this->withAuthorization()` to add the Authorization header
- **Request Body**: Use `setJson()` for JSON payloads, `setUrlEncodedData()` for form-encoded data
- **PHPDoc**: Include comprehensive documentation with description, parameter types, return type, and exceptions
- **Data Structures**: For endpoint parameters, prefer flexible arrays passed directly as JSON. See `.cursor/rules/data-structures.mdc` for detailed guidelines on when to use flexible arrays vs structured types (enums).

### Example: GET Endpoint

```php
public function getCurrentUser(): HttpClientResponse
{
    $this->client
        ->prepareRequest('GET', $this->baseUri . '/users/@me')
        ->setHeader('Content-Type', 'application/json');

    $this->withAuthorization();

    return $this->client->execute();
}
```

### Example: PUT Endpoint with Body

```php
public function bulkGlobalApplicationCommands(string $applicationId, array $commands): HttpClientResponse
{
    $this->client
        ->prepareRequest(
            'PUT',
            $this->baseUri . '/applications/' . $applicationId . '/commands'
        )
        ->setHeader('Content-Type', 'application/json');

    $this->withAuthorization();

    $this->client->getRequest()->setJson($commands);

    return $this->client->execute();
}
```

## 2. Integration Test File

### File Location

Create a new test file in `tests/Integration/DiscordClient/{Feature}Test.php`

### Test Class Structure

```php
<?php

namespace Tests\EasyHttp\DiscordClient\Integration\DiscordClient;

use EasyHttp\DiscordClient\Authorization;
use EasyHttp\DiscordClient\DiscordClient;
use EasyHttp\MockBuilder\HttpMock;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use Tests\EasyHttp\DiscordClient\Integration\Concerns\HasAuthorization;
use Tests\EasyHttp\DiscordClient\Integration\IntegrationTestCase;

#[CoversClass(DiscordClient::class)]
#[UsesClass(Authorization::class)]
class {Feature}Test extends IntegrationTestCase
{
    use HasAuthorization;

    #[Test]
    #[DataProvider('authorizationProvider')]
    public function itCan[Description](Authorization $authorization): void
    {
        // 1. Set up test data using $this->faker
        $testData = $this->faker->...;

        // 2. Configure mock response
        $this->builder
            ->when()
                ->methodIs('HTTP_METHOD')
                ->pathIs('/api/v10/endpoint/path')
                // OR use pathMatch() for dynamic paths:
                // ->pathMatch('/api\/v10\/endpoint\/' . $dynamicId . '/')
            ->then()
                ->statusCode(200)
                ->json($jsonResponse = [
                    // Mock response data
                ]);

        // 3. Create client and execute
        $client = new DiscordClient($authorization);
        $client->withHandler(new HttpMock($this->builder));
        $response = $client->methodName(...);

        // 4. Assert results
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame($jsonResponse, $response->parseJson());
    }

    // Required by HasAuthorization trait
    // IMPORTANT: This method should only test ONE endpoint per test class.
    // If you need to test multiple endpoints, create separate test files.
    protected function invokeEndpoint(DiscordClient $client): void
    {
        // Call the endpoint method with test data
        $client->methodName(...);
    }
}
```

### Key Points:

- **Extend**: `IntegrationTestCase` (provides `$this->faker` and `$this->builder`)
- **Use Trait**: `HasAuthorization` (provides authorization tests and `authorizationProvider()`)
- **Attributes**:
  - `#[CoversClass(DiscordClient::class)]` - Required
  - `#[UsesClass(Authorization::class)]` - Required
  - `#[Test]` - Marks test methods
  - `#[DataProvider('authorizationProvider')]` - Tests with both Bearer and Bot tokens
- **Mock Setup**: Use `MockBuilder` to configure expected requests and responses
- **Path Matching**: Use `pathIs()` for exact paths, `pathMatch()` with regex for dynamic paths
- **invokeEndpoint()**: Required abstract method from `HasAuthorization` trait - used for testing authorization exceptions. **IMPORTANT**: This method should only test ONE endpoint per test class. If you need to test multiple endpoints, create separate test files for each endpoint.

### Example Test

```php
#[Test]
#[DataProvider('authorizationProvider')]
public function itCanGetCurrentUserInfo(Authorization $authorization): void
{
    $this->builder
        ->when()
            ->methodIs('GET')
            ->pathIs('/api/v10/users/@me')
        ->then()
            ->statusCode(200)
            ->json(
                $jsonResponse = [
                    'id' => $this->faker->numerify('###################'),
                    'username' => $this->faker->userName,
                    'global_name' => $this->faker->name,
                    'avatar' => '6c5996770c985bcd6e5b68131ff2ba04',
                    'verified' => true,
                ]
            );

    $client = new DiscordClient($authorization);
    $client->withHandler(new HttpMock($this->builder));
    $response = $client->getCurrentUser();

    $this->assertSame(200, $response->getStatusCode());
    $this->assertSame($jsonResponse, $response->parseJson());
}

protected function invokeEndpoint(DiscordClient $client): void
{
    $client->getCurrentUser();
}
```

## 3. Example File

### File Location

Create example files in `examples/{category}/{feature_name}.php`

Organize examples by category (e.g., `users/`, `commands/`, `oauth2/`)

### Example File Structure

```php
<?php

require_once(__DIR__ . '/../../vendor/autoload.php');

use EasyHttp\DiscordClient\Authorization;
use EasyHttp\DiscordClient\Constants\AuthorizationType;
use EasyHttp\DiscordClient\DiscordClient;

// 1. Set up authorization
$token = 'your-bot-or-bearer-token-here';
$authorizationType = AuthorizationType::BOT_TOKEN;
// Add comments about alternative authorization types and required scopes

$authorization = new Authorization();
$authorization->setAuthorization($authorizationType, $token);

// 2. Create client
$client = new DiscordClient($authorization);

// 3. Prepare request data (if needed)
$data = [
    // Request payload
];

// 4. Call endpoint
$response = $client->methodName(...);

// 5. Display results
var_dump($response->getStatusCode(), $response->getBody());
```

### Key Points:

- **Autoload**: Always include `vendor/autoload.php`
- **Comments**: Include helpful comments about authorization types and required scopes
- **Placeholders**: Use descriptive placeholders like `'your-bot-or-bearer-token-here'`
- **Output**: Use `var_dump()` to show status code and response body

### Example

```php
<?php

require_once(__DIR__ . '/../../vendor/autoload.php');

use EasyHttp\DiscordClient\Authorization;
use EasyHttp\DiscordClient\Constants\AuthorizationType;
use EasyHttp\DiscordClient\DiscordClient;

$token = 'your-bot-or-bearer-token-here';
$authorizationType = AuthorizationType::BOT_TOKEN;
// you can also use AuthorizationType::BEARER_TOKEN with a token that has "identify"/"email" scopes

$authorization = new Authorization();
$authorization->setAuthorization($authorizationType, $token);

$client = new DiscordClient($authorization);

$response = $client->getCurrentUser();

var_dump($response->getStatusCode(), $response->getBody());
```

## 4. Constants/Enums (When Needed)

### File Location

Create enum files in `src/Constants/{EnumName}.php` if the endpoint uses specific constants.

### Enum Structure

```php
<?php

namespace EasyHttp\DiscordClient\Constants;

enum EnumName: int
{
    case VALUE_NAME = 0;
    case ANOTHER_VALUE = 1;

    // Add PHPDoc comments for clarity
    /**
     * Description of what this value represents
     */
    case DOCUMENTED_VALUE = 2;
}
```

### Key Points:

- **Backed Enums**: Use backed enums with `int` type
- **Naming**: Use SCREAMING_SNAKE_CASE for enum cases
- **Documentation**: Add PHPDoc comments for complex values
- **Usage**: Reference enum values in tests using `EnumName::VALUE_NAME->value`

### Example

```php
<?php

namespace EasyHttp\DiscordClient\Constants;

enum InteractionContextType: int
{
    /**
     * Interaction can be used within servers (guilds)
     */
    case GUILD = 0;

    /**
     * Interaction can be used within DMs with the app's bot user
     */
    case BOT_DM = 1;

    /**
     * Interaction can be used within Group DMs and DMs other than the app's bot user
     */
    case PRIVATE_CHANNEL = 2;
}
```

## 5. Authorization Handling

### Automatic Authorization

The `withAuthorization()` method automatically handles:
- Bearer Token: Sets `Authorization: Bearer {token}` header
- Bot Token: Sets `Authorization: Bot {token}` header
- Validation: Throws `MissingTokenException` or `MissingAuthorizationTypeException` if not set

### Testing Authorization

The `HasAuthorization` trait automatically provides:
- Test for missing token (`itThrowsAnExceptionWhenTokenIsMissing`)
- Test for missing authorization type (`itThrowsAnExceptionWhenAuthTypeIsMissing`)
- Data provider for both Bearer and Bot token authorization (`authorizationProvider`)

## Checklist

When implementing a new endpoint, ensure:

- [ ] Method added to `DiscordClient.php` with proper PHPDoc
- [ ] Method uses `$this->baseUri` for the base URL
- [ ] Method calls `$this->withAuthorization()`
- [ ] Method sets appropriate Content-Type header
- [ ] Method handles request body correctly (if applicable)
- [ ] Complex request payloads accept `array` parameters and pass directly to `setJson()` (see `.cursor/rules/data-structures.mdc` for guidelines)
- [ ] Integration test file created in `tests/Integration/DiscordClient/`
- [ ] Test class extends `IntegrationTestCase` and uses `HasAuthorization` trait
- [ ] Test method uses `#[DataProvider('authorizationProvider')]` to test both auth types
- [ ] Test implements `invokeEndpoint()` method (one endpoint per test class)
- [ ] Test uses `MockBuilder` to configure expected requests/responses
- [ ] Test asserts status code and response body
- [ ] Example file created in appropriate `examples/{category}/` directory
- [ ] Example includes comments about authorization types and scopes
- [ ] Constants/Enums created if needed in `src/Constants/`
- [ ] All files follow PSR-12 coding standards

## Notes

- **Path Matching**: For dynamic paths (e.g., with IDs), use `pathMatch()` with regex in tests
- **Faker**: Use `$this->faker` in tests for generating test data
- **Mock Responses**: Match the actual Discord API response structure in mock responses
- **Error Cases**: Consider adding tests for error cases (4xx, 5xx responses) when appropriate
- **Scopes**: Document required OAuth2 scopes in example file comments
- **Data Structures**: For endpoint parameters, use flexible arrays. For common API options, use structured enums. See `.cursor/rules/data-structures.mdc` for detailed guidelines.
