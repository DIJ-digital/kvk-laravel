# AGENTS.md

## Project Overview

Laravel package (`DIJ/kvk-laravel`) wrapping the Dutch KVK (Kamer van Koophandel) API. Provides a clean, fakable interface for searching the KVK company registry from any Laravel application.

- **Namespace**: `DIJ\Kvk`
- **PHP**: ^8.5
- **Laravel**: ^12
- **License**: MIT

## Setup

```bash
composer install
```

No build step. This is a library — never a standalone application.

## Commands

| Command | Purpose |
|---|---|
| `composer analyse` | PHPStan static analysis (level 10, strict rules) |
| `composer pint` | Code formatting (Laravel Pint) |
| `composer rector` | Automated code upgrades |
| `composer rector:dry-run` | Preview rector changes without applying |
| `vendor/bin/phpunit` | Run tests |
| `vendor/bin/phpunit --coverage-text` | Run tests with coverage report |

Always run `composer analyse` and `vendor/bin/phpunit` before committing. Both must pass clean.

## Architecture

```
src/
├── Collections/        # Typed collections extending Illuminate Collection
├── Data/               # Immutable DTOs (parameters, responses, settings)
├── Exceptions/         # Typed exception hierarchy for API errors
├── Facades/            # Laravel facade(s) — thin proxies only
├── Repositories/       # One repository per KVK API resource/endpoint
├── Testing/            # Fake implementations for consumer test support
├── KVK.php             # Public API entry point — returns repositories
├── KVKGateway.php      # HTTP client wrapper — all HTTP concerns live here
└── KVKServiceProvider.php  # Service provider — bindings and config only
config/
└── kvk.php             # Published config file
```

### Layer Responsibilities

- **`KVK`** — Single entry point for consumers. Returns repository instances. Contains zero business logic or HTTP calls.
- **`KVKGateway`** — Wraps `Illuminate\Http\Client\Factory`. Handles base URL, authentication headers, and delegates HTTP calls. All HTTP configuration lives here and nowhere else.
- **`Repositories/`** — One class per API resource (e.g., `SearchRepository`). Accepts typed parameter DTOs, calls the gateway, returns typed collection/DTO responses. A repository never constructs HTTP requests directly — it calls the gateway.
- **`Data/`** — Pure immutable value objects. No dependencies on framework classes. Constructable via named constructor arguments or `fromArray()` static factory. Every DTO property must be typed.
- **`Collections/`** — Typed collections extending `Illuminate\Support\Collection`. Contain a `fromResult()` static factory that parses API responses into typed items.
- **`Exceptions/`** — Typed exception hierarchy. All exceptions extend `KvkException` (which extends `RuntimeException`) and expose `statusCode` and `responseBody` properties. Subclasses: `KvkAuthenticationException` (401/403), `KvkServerException` (500+), `KvkRequestException` (other). Thrown exclusively by `KVKGateway`.
- **`Facades/`** — Standard Laravel facades. Must include `@method` docblocks for every proxied method. No logic.
- **`Testing/`** — Fake implementations (`FakeKVK`, `FakeSearchRepository`, etc.) returned by `KVK::fake()`. Allow consumers to test without HTTP calls. Each fake mirrors its real counterpart's public API with canned responses.
- **`KVKServiceProvider`** — Registers singletons, merges config, publishes config. No business logic.

### Adding a New API Endpoint

1. Create parameter DTO in `Data/` (e.g., `ProfileParameters`)
2. Create response DTO in `Data/` (e.g., `ProfileResponse`)
3. Create typed collection in `Collections/` (e.g., `ProfileResponseCollection`) with `fromResult()` factory
4. Create repository in `Repositories/` (e.g., `ProfileRepository`) that accepts the parameter DTO and returns the collection
5. Add a method on `KVK.php` that returns the new repository
6. Add `@method` docblock on the Facade
7. Write unit tests covering all of the above with 100% line coverage
8. Update `.opencode/skills/kvk-api/SKILL.md` — add endpoint, parameters, response shape, test data, and field mappings
9. Update `resources/boost/skills/kvk-api-development/SKILL.md` — add consumer-facing usage examples, fluent builder docs, and faking support
10. Update `resources/boost/guidelines/core.blade.php` if the new endpoint changes the package overview or quick example
11. Update `README.md` — add usage documentation for the new endpoint

## Code Style & Conventions

### Strict PHP

- Every file starts with `declare(strict_types=1);`  — enforced by Rector
- PHPStan level 10 with `phpstan-strict-rules` — no exceptions, no baselines
- Never use `@phpstan-ignore` unless absolutely unavoidable (document why in a comment if so)
- Never use `mixed` types — always specify concrete types
- No magic methods except `KVKGateway::__call()` (which is the single controlled delegation point to the HTTP client)

### Formatting

- Laravel Pint handles all formatting — never manually adjust code style
- Run `composer pint` to format; CI will reject unformatted code

### Naming

- Classes: `PascalCase`
- Methods/properties: `camelCase`
- Config keys: `snake_case`
- DTO properties: `snake_case` (matching config/API conventions)
- KVK API parameter mapping uses Dutch names in `toArray()` (e.g., `kvkNummer`, `plaats`) — keep English property names on the DTO, Dutch in the API payload
- All code (classes, methods, properties, variables) must use **English names**. Dutch naming is only permitted in: API URL paths (e.g., `api/v1/basisprofielen/{kvkNummer}/eigenaar` — these are KVK API paths), `fromArray()`/`toArray()` key mappings (e.g., `'kvkNummer' => $this->kvkNumber` — these map Dutch API field names to English properties), and PHPDoc `@param array{...}` shapes that describe API response structures

### Dependencies

- **Minimize dependencies.** Only `illuminate/http` and `illuminate/support` are allowed as runtime dependencies.
- Never add a Guzzle, HTTP, or JSON dependency directly — use Illuminate's HTTP client.
- Dev dependencies: PHPStan, Rector, Pint, PHPUnit only. No additional dev packages without explicit justification.

### Patterns

- **Constructor injection everywhere** — no `app()` helper, no `resolve()` calls outside the service provider
- **Readonly/immutable data objects** — DTOs should not have setters or mutable state
- **Static factories on data objects** — use `fromArray()`, `fromResult()` etc. for construction from raw data
- **Single responsibility** — each class has one reason to change
- **No service locator** — the service provider is the only place that touches the container

## Testing

### Requirements

- **100% line coverage** — no exceptions
- **Every public method** must have at least one test
- **Unit tests only** — no HTTP calls, no database, no filesystem
- Tests live in `tests/` mirroring the `src/` structure (e.g., `tests/Repositories/SearchRepositoryTest.php`)

### Faking the KVK Client

The package is designed to be fakable through Laravel's Facade and HTTP client faking:

```php
// Option 1: Facade fake (for consumers of the package)
KVK::fake();

// Option 2: HTTP fake (for testing within the package itself)
Http::fake([
    'api.kvk.nl/*' => Http::response([...], 200),
]);
```

When writing tests inside this package, use `Http::fake()` to mock the KVK API responses. Never make real HTTP calls in tests.

### Test Structure

```php
declare(strict_types=1);

namespace DIJ\Kvk\Tests\Repositories;

use PHPUnit\Framework\TestCase;

final class SearchRepositoryTest extends TestCase
{
    // Group tests by method: test_{method}_{scenario}
    public function test_search_returns_collection(): void { }
    public function test_search_passes_parameters_to_gateway(): void { }
}
```

- All test classes are `final`
- Test method names: `test_{method}_{scenario}` (snake_case)
- Use `self::assert*()` over `$this->assert*()`
- Prefer specific assertions (`assertCount`, `assertInstanceOf`) over generic ones (`assertTrue`)

## API Surface for Package Consumers

The public API that consuming Laravel projects interact with:

```php
use DIJ\Kvk\Facades\KVK;
use DIJ\Kvk\Data\SearchParameters;

// Search the KVK registry
$params = new SearchParameters(name: 'Acme', city: 'Amsterdam');
$results = KVK::search()->search($params);
```

### What is Public API

- `KVK` facade and its methods
- `KVK::class` main entry point
- All classes in `Data/` (DTOs are part of the contract)
- All classes in `Collections/`
- Repository public methods

### What is Internal

- `KVKGateway` — internal HTTP delegation, not for consumers
- `KVKServiceProvider` — auto-discovered, not called directly
- Any `protected`/`private` method

Never break the public API without a major version bump.

## Security

- API keys are stored in config, loaded from environment variables (`KVK_API_KEY`)
- Never hardcode API keys or credentials
- Never log full API responses in production (may contain business-sensitive data)
- The default API key in config (`1234`) is a placeholder for development only

## PR & Commit Guidelines

- Commit messages: imperative mood, concise (`Add profile endpoint`, `Fix search parameter mapping`)
- One concern per commit
- PRs must pass: PHPStan, Pint, PHPUnit (100% coverage)
- No `composer.lock` in the repository (library, not application)
