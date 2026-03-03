# KVK Laravel

A Laravel package for the Dutch KVK (Kamer van Koophandel) Handelsregister API. Provides a clean, typed, and fakable interface for searching the company registry.

## Requirements

- PHP ^8.4|^8.5
- Laravel ^12

## Installation

```bash
composer require dij-digital/kvk-laravel
```

The service provider and facade are auto-discovered.

Publish the config file:

```bash
php artisan vendor:publish --tag=kvk
```

## Configuration

Add your API key to `.env`:

```env
KVK_API_KEY=your-api-key
KVK_BASE_URL=https://api.kvk.nl
```

For testing against the KVK sandbox, use:

```env
KVK_API_KEY=l7xx1f2691f2520d487b902f4e0b57a0b197
KVK_BASE_URL=https://api.kvk.nl/test
```

## Usage

### Search

Search the KVK registry using the fluent API:

```php
use DIJ\Kvk\Facades\KVK;

$result = KVK::search()
    ->kvkNumber('69599068')
    ->city('Amsterdam')
    ->includeInactiveRegistrations()
    ->get();

foreach ($result->items as $item) {
    echo $item->kvkNumber;  // '69599068'
    echo $item->name;       // 'Test BV Donald'
    echo $item->type;       // 'hoofdvestiging'
    echo $item->active;     // 'Ja'
}
```

Or pass a `SearchParameters` DTO directly:

```php
use DIJ\Kvk\Data\SearchParameters;
use DIJ\Kvk\Facades\KVK;

$params = new SearchParameters(
    kvkNumber: '69599068',
    city: 'Amsterdam',
    includeInactiveRegistrations: true,
);
$result = KVK::search()->search($params);
```

Both approaches produce identical API calls.

### Available search parameters

| Method | Type | Description |
|---|---|---|
| `kvkNumber(string)` | string | 8-digit KVK number |
| `rsin(string)` | string | 9-digit RSIN number |
| `branchNumber(string)` | string | 12-digit branch number |
| `name(string)` | string | Trade name or statutory name |
| `streetName(string)` | string | Street name |
| `city(string)` | string | City name |
| `postalCode(string)` | string | Postal code (with houseNumber or poBoxNumber) |
| `houseNumber(int)` | int | House number (with postalCode) |
| `houseLetter(string)` | string | House letter (with houseNumber) |
| `poBoxNumber(int)` | int | PO box number (with postalCode) |
| `type(array)` | array | Filter: `hoofdvestiging`, `nevenvestiging`, `rechtspersoon` |
| `includeInactiveRegistrations(bool)` | bool | Include dissolved companies (default: false) |
| `page(int)` | int | Page number (default: 1) |
| `resultsPerPage(int)` | int | Results per page (default: 100, max: 100) |

### Base Profile

Retrieve detailed company information by KVK number using the fluent API:

```php
use DIJ\Kvk\Facades\KVK;

// Get main company profile
$profile = KVK::baseProfile('69599068')->get();
echo $profile->kvkNumber;    // '69599068'
echo $profile->name;         // 'Test Stichting Bolderbast'
echo $profile->statutoryName; // 'Stichting Bolderbast'

// Get owner information
$owner = KVK::baseProfile('69599068')->owner();
echo $owner->legalForm;      // 'BesloteVennootschap'
foreach ($owner->addresses as $address) {
    echo $address->city;     // 'Woerden'
}

// Get main branch details
$mainBranch = KVK::baseProfile('69599068')->mainBranch();
echo $mainBranch->branchNumber;    // '000037178598'
echo $mainBranch->totalEmployees;  // 15

// Get all branches listing
$branches = KVK::baseProfile('69599068')->branches();
echo $branches->totalBranchCount;  // 1
foreach ($branches->branches as $branch) {
    echo $branch->firstTradeName;  // 'Test BV Donald'
}

// Include GPS coordinates in addresses (geoData)
$owner = KVK::baseProfile('69599068')->geoData()->owner();
foreach ($owner->addresses as $address) {
    echo $address->geoData?->gpsLatitude;  // 52.08151653230184
}
```

### Available base profile options

| Method | Description |
|---|---|
| `get()` | Main company profile — name, registration dates, trade names, SBI activities |
| `owner()` | Owner information — legal form, addresses, websites |
| `mainBranch()` | Main branch details — employees, addresses, trade names, SBI activities |
| `branches()` | All branches listing — counts and summary per branch |
| `geoData(bool)` | Include GPS coordinates in address responses (default: false) |

### Branch Profile

Retrieve detailed branch information by branch number (`vestigingsnummer`):

```php
use DIJ\Kvk\Facades\KVK;

$branch = KVK::branchProfile('000037178598')->get();
echo $branch->branchNumber;   // '000037178598'
echo $branch->kvkNumber;      // '68750110'
echo $branch->firstTradeName; // 'Test BV Donald'

// Include GPS coordinates in addresses
$branch = KVK::branchProfile('000037178598')->geoData()->get();
foreach ($branch->addresses as $address) {
    echo $address->geoData?->gpsLatitude;
}
```

### Available branch profile options

| Method | Description |
|---|---|
| `get()` | Branch profile — trade names, employees, addresses, websites, and SBI activities |
| `geoData(bool)` | Include GPS coordinates in address responses (default: false) |

### Naming (Trade Names)

Retrieve trade names by KVK number:

```php
use DIJ\Kvk\Facades\KVK;

$naming = KVK::naming('69599068')->get();
echo $naming->kvkNumber;     // '69599068'
echo $naming->statutoryName; // 'Stichting Bolderbast'
echo $naming->name;          // 'Test Stichting Bolderbast'

foreach ($naming->branches as $branch) {
    echo $branch->branchNumber;     // '000037178598'
    echo $branch->firstTradeName;   // 'Test Stichting Bolderbast' (commercial)
    echo $branch->name;             // 'Stichting Branch' (non-commercial)
    echo $branch->alsoKnownAs;      // 'Branch Alias'
}
```

### Available naming options

| Method | Description |
|---|---|
| `get()` | Trade names profile — statutory name, primary name, aliases, and branch-level names |

### Subscriptions (Mutatieservice)

Monitor changes to KVK registry entries via the Mutatieservice (change notifications):

```php
use DIJ\Kvk\Facades\KVK;

// List all subscriptions
$subscriptions = KVK::subscriptions()->get();
echo $subscriptions->customerId;  // 'customer-123'
foreach ($subscriptions->subscriptions as $sub) {
    echo $sub->id;         // 'subscription-456'
    echo $sub->startDate;  // '2024-01-01T00:00:00Z'
    echo $sub->active;     // true
}

// Get signals (change notifications) for a subscription
$signals = KVK::subscriptions()
    ->subscription('subscription-456')
    ->from('2024-01-01T00:00:00Z')
    ->to('2024-12-31T23:59:59Z')
    ->page(1)
    ->resultsPerPage(50)
    ->signals();

foreach ($signals->signals as $signal) {
    echo $signal->kvkNumber;   // '69792917'
    echo $signal->signalType;  // 'SignaalGewijzigdeInschrijving'
    echo $signal->timestamp;   // '2024-05-14T15:25:13.773Z'
}

// Get a specific signal's full details
$signal = KVK::subscriptions()
    ->subscription('subscription-456')
    ->signal('signal-001');

echo $signal->messageId;              // '3e96fad5-...'
echo $signal->signalType;             // 'SignaalGewijzigdeInschrijving'
echo $signal->registrationTimestamp;  // '2024-05-14T15:25:13.773Z'
echo $signal->relatesTo['kvkNummer']; // '69792917'
```

### Available subscription options

| Method | Description |
|---|---|
| `get()` | List all subscriptions for your API key |
| `subscription(string)` | Scope to a specific subscription (returns a scoped builder) |

### Available signal options (on scoped subscription)

| Method | Description |
|---|---|
| `from(string)` | Filter signals from this ISO 8601 datetime |
| `to(string)` | Filter signals until this ISO 8601 datetime |
| `page(int)` | Page number (default: 1) |
| `resultsPerPage(int)` | Results per page (min: 10, max: 500, default: 100) |
| `signals()` | List signals for this subscription (paginated) |
| `signal(string)` | Get a specific signal's full details |

## Error Handling

The package throws typed exceptions for API failures. All exceptions extend `KvkException` and expose `statusCode` and `responseBody` properties.

| Exception | When |
|---|---|
| `KvkAuthenticationException` | HTTP 401 or 403 — invalid or missing API key |
| `KvkServerException` | HTTP 500+ — KVK API server error |
| `KvkRequestException` | Any other non-successful HTTP response |

```php
use DIJ\Kvk\Exceptions\KvkAuthenticationException;
use DIJ\Kvk\Exceptions\KvkException;

try {
    $result = KVK::search()->kvkNumber('69599068')->get();
} catch (KvkAuthenticationException $e) {
    // Invalid API key — check KVK_API_KEY in .env
    $e->statusCode;    // 401
    $e->responseBody;  // raw response body
} catch (KvkException $e) {
    // Any other API error
    $e->statusCode;
    $e->responseBody;
}
```

## Testing

The package is designed to be easily fakeable in your application tests.

### Basic Usage

Use `KVK::fake()` to replace all KVK API calls with a fake that returns no results:

```php
use DIJ\Kvk\Facades\KVK;

KVK::fake();

$result = KVK::search()->kvkNumber('69599068')->get();
// Returns a SearchResult with 0 items — no HTTP calls made
```

### Faking Specific Responses

Pass response DTOs to `KVK::fake()` using named parameters to control what each endpoint returns:

```php
use DIJ\Kvk\Data\Responses\BaseProfileResponse;
use DIJ\Kvk\Data\Responses\SearchResponse;
use DIJ\Kvk\Data\ValueObjects\SbiActivity;
use DIJ\Kvk\Facades\KVK;

// Search faking — use withSearchResponses() on the returned FakeKVK
KVK::fake()->withSearchResponses(
    SearchResponse::fake(kvkNumber: '69599068', name: 'Acme BV'),
    SearchResponse::fake(kvkNumber: '12345678', name: 'Other BV'),
);

$result = KVK::search()->get();
// $result->total === 2
// $result->items->first()->kvkNumber === '69599068'

// Customize nested data — e.g., test a specific SBI code
KVK::fake(
    baseProfile: BaseProfileResponse::fake(
        sbiActivities: [SbiActivity::fake(sbiCode: '86101')],
    ),
);

$profile = KVK::baseProfile('69599068')->get();
echo $profile->sbiActivities[0]->sbiCode; // '86101'
```

#### Available fake() parameters

| Parameter | Type | Description |
|---|---|---|
| `$baseProfile` | `?BaseProfileResponse` | Custom base profile response |
| `$baseProfileOwner` | `?BaseProfileOwnerResponse` | Custom owner response |
| `$baseProfileMainBranch` | `?BaseProfileMainBranchResponse` | Custom main branch response |
| `$baseProfileBranches` | `?BaseProfileBranchesResult` | Custom branches result |
| `$branchProfile` | `?BranchProfileResponse` | Custom branch profile response |
| `$naming` | `?NamingResponse` | Custom naming response |
| `$subscriptions` | `?SubscriptionsResult` | Custom subscriptions result |
| `$signals` | `?SignalsResult` | Custom signals result |
| `$signal` | `?SignalResponse` | Custom signal response |
| `...$searchResponses` | `SearchResponse` | Search responses — pass via `withSearchResponses()` on the returned `FakeKVK` |

#### Fluent Builder Interface

You can also update a fake after it's been created using the fluent `with*` methods:

```php
KVK::fake()
    ->withBaseProfile(BaseProfileResponse::fake(name: 'Updated Name'))
    ->withSearchResponses(SearchResponse::fake(kvkNumber: '11223344'));
```

#### DTO Fake Defaults

Every response DTO provides a `fake()` method with sensible defaults from the KVK test environment. Only specify the fields you need to override:

```php
$response = SearchResponse::fake(
    kvkNumber: '69599068',
    name: 'Test BV Donald',
    type: 'hoofdvestiging',
);
```

| Parameter | Default |
|---|---|
| `kvkNumber` | `'69599068'` |
| `name` | `'Test BV Donald'` |
| `type` | `'hoofdvestiging'` |
| `active` | `'Ja'` |
| `branchNumber` | `'000037178598'` |
| `rsin` | `null` |
| `address` | `null` |

### Facade Cleanup

If you use `KVK::fake()` in tests that extend Laravel's `TestCase`, cleanup is automatic. For plain PHPUnit tests, add this to your test class:

```php
protected function tearDown(): void
{
    \Illuminate\Support\Facades\Facade::clearResolvedInstances();
    parent::tearDown();
}
```

### Advanced: HTTP-Level Faking

For lower-level tests that need to verify the full HTTP pipeline (including request parameters and response parsing), you can still use `Http::fake()` directly:

```php
use Illuminate\Support\Facades\Http;

Http::fake([
    'api.kvk.nl/*' => Http::response([
        'pagina' => 1,
        'resultatenPerPagina' => 10,
        'totaal' => 1,
        'resultaten' => [
            [
                'kvkNummer' => '69599068',
                'naam' => 'Test BV',
                'type' => 'hoofdvestiging',
                'actief' => 'Ja',
            ],
        ],
    ]),
]);

$result = KVK::search()->kvkNumber('69599068')->get();
```

## License

MIT
