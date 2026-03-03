---
name: kvk-api-development
description: "Search and query the Dutch KVK (Kamer van Koophandel) company registry. Activates when looking up companies, searching KVK numbers, querying trade register data, working with KVK API responses, or testing KVK API calls; or when the user mentions KVK, Kamer van Koophandel, company search, Dutch business registry, trade register, handelsregister, or company lookup."
license: MIT
metadata:
  author: hanwoolderink
---

# KVK API Development

## When to Apply

Activate this skill when:

- Searching the Dutch KVK company registry
- Looking up companies by KVK number, name, or address
- Working with KVK API responses (parsing results, iterating companies)
- Testing or mocking KVK API calls
- Configuring KVK API credentials

## Configuration

### Environment variables

Add to `.env`:

<!-- Environment Setup -->
```env
KVK_API_KEY=your-api-key
KVK_BASE_URL=https://api.kvk.nl
```

For the KVK sandbox/test environment:

<!-- Sandbox Setup -->
```env
KVK_API_KEY=l7xx1f2691f2520d487b902f4e0b57a0b197
KVK_BASE_URL=https://api.kvk.nl/test
```

### Publishing config

<!-- Publish Config -->
```bash
php artisan vendor:publish --tag=kvk
```

This publishes `config/kvk.php` with two keys: `base_url` and `api_key`.

## Search API

The search API wraps `GET /api/v2/zoeken`. There are two equivalent approaches:

### Fluent builder (recommended)

<!-- Fluent Search -->
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

### DTO approach

<!-- DTO Search -->
```php
use DIJ\Kvk\Data\Parameters\SearchParameters;
use DIJ\Kvk\Facades\KVK;

$params = new SearchParameters(
    kvkNumber: '69599068',
    city: 'Amsterdam',
    includeInactiveRegistrations: true,
);
$result = KVK::search()->search($params);
```

Both approaches produce identical API calls.

## Available Search Parameters

| Fluent method | DTO property | Type | Description |
|---|---|---|---|
| `kvkNumber(string)` | `kvkNumber` | `?string` | 8-digit KVK number |
| `rsin(string)` | `RSIN` | `?string` | 9-digit RSIN number |
| `branchNumber(string)` | `branchNumber` | `?string` | 12-digit branch number (vestigingsnummer) |
| `name(string)` | `name` | `?string` | Trade name or statutory name |
| `streetName(string)` | `streetName` | `?string` | Street name |
| `city(string)` | `city` | `?string` | City name |
| `postalCode(string)` | `postalCode` | `?string` | Postal code (requires houseNumber or poBoxNumber) |
| `houseNumber(int)` | `houseNumber` | `?int` | House number (requires postalCode) |
| `houseLetter(string)` | `houseLetter` | `?string` | House letter (requires houseNumber) |
| `poBoxNumber(int)` | `poBoxNumber` | `?int` | PO box number (requires postalCode) |
| `type(array)` | `type` | `?array` | Filter: `'hoofdvestiging'`, `'nevenvestiging'`, `'rechtspersoon'` |
| `includeInactiveRegistrations(bool)` | `includeInactiveRegistrations` | `?bool` | Include dissolved companies (default: false) |
| `page(int)` | `page` | `?int` | Page number (default: 1) |
| `resultsPerPage(int)` | `resultsPerPage` | `?int` | Results per page (default: 100, max: 100) |

## Response Handling

`get()` and `search()` return a `SearchResult` with:

<!-- Response Structure -->
```php
$result = KVK::search()->name('Acme')->get();

$result->items;          // SearchResponseCollection (extends Illuminate\Support\Collection)
$result->page;           // int — current page number
$result->resultsPerPage; // int — results per page
$result->total;          // int — total result count
$result->previous;       // ?string — link to previous page
$result->next;           // ?string — link to next page
```

Each item in `$result->items` is a `SearchResponse`:

<!-- Item Properties -->
```php
foreach ($result->items as $item) {
    $item->kvkNumber;    // string — '69599068'
    $item->name;         // string — 'Test BV Donald'
    $item->type;         // string — 'hoofdvestiging', 'nevenvestiging', or 'rechtspersoon'
    $item->active;       // string — 'Ja' or 'Nee'
    $item->rsin;         // ?string
    $item->branchNumber; // ?string — 12-digit vestigingsnummer
    $item->address;      // ?SearchResultAddress (with ->domesticAddress and ->foreignAddress sub-objects)
    $item->expiredName;  // ?string — expired trade name that matched the search
    $item->links;        // array<Link> — HATEOAS links to related resources
}
```

### Result type values

| Type | Meaning |
|---|---|
| `hoofdvestiging` | Main branch — has basisprofiel and vestigingsprofiel links |
| `nevenvestiging` | Secondary branch — has both links |
| `rechtspersoon` | Legal entity — has basisprofiel link only, no branchNumber |

## Error Handling

The package throws typed exceptions for API failures. All exceptions extend `KvkException`, which exposes `->statusCode` (int) and `->responseBody` (string).

| Exception | When |
|---|---|
| `KvkAuthenticationException` | HTTP 401 or 403 — invalid or missing API key |
| `KvkServerException` | HTTP 500+ — KVK API server error |
| `KvkRequestException` | Any other non-successful HTTP response |

<!-- Error Handling -->
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
    // Any other API error (KvkRequestException, KvkServerException)
    $e->statusCode;    // HTTP status code
    $e->responseBody;  // raw response body
}
```

## Testing

The package is designed to be easily fakeable in your application tests.

### Basic Usage
,
Use `KVK::fake()` to replace all KVK API calls with a fake that returns no results:

<!-- KVK Facade Fake -->
```php
use DIJ\Kvk\Facades\KVK;

KVK::fake();

$result = KVK::search()->kvkNumber('69599068')->get();
// Returns a SearchResult with 0 items — no HTTP calls made
```

### Faking Specific Responses
,
Pass response DTOs to `KVK::fake()` using named parameters to control what each endpoint returns:

<!-- KVK Facade Fake with Responses -->
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
,
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
,
You can also update a fake after it's been created using the fluent `with*` methods:

```php
KVK::fake()
    ->withBaseProfile(BaseProfileResponse::fake(name: 'Updated Name'))
    ->withSearchResponses(SearchResponse::fake(kvkNumber: '11223344'));
```

#### DTO Fake Defaults
,
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
| `address` | `null` |],op:

The API response uses **Dutch field names** (`kvkNummer`, `naam`, `actief`). The package DTOs translate these to English properties. When faking with `Http::fake()`, use the Dutch names in the response array.

## Common Pitfalls

- **Exact word matching only** — the KVK search API matches complete words. Searching `koophand` returns 0 results; `koophandel` works.
- **Inactive registrations excluded by default** — call `->includeInactiveRegistrations()` or set the parameter to `true` to include dissolved companies.
- **Max 100 results per page** — `resultsPerPage` caps at 100. Use `page()` to paginate beyond that.
- **Dutch response fields** — when faking HTTP responses, use the Dutch API field names (`kvkNummer`, `naam`, `plaats`, `actief`) not the English DTO property names.
- **postalCode requires a partner** — `postalCode` only works combined with `houseNumber` or `poBoxNumber`.
- **Type values are Dutch** — filter values are `hoofdvestiging`, `nevenvestiging`, `rechtspersoon` (not English translations).

## Base Profile API

Retrieve detailed company profiles using a KVK number. The API provides the main profile, owner information, main branch details, and a listing of all branches.

### Usage

Use the `baseProfile()` method on the facade with an 8-digit KVK number:

<!-- Base Profile Usage -->
```php
use DIJ\Kvk\Facades\KVK;

// Get main company profile
$profile = KVK::baseProfile('69599068')->get();
echo $profile->kvkNumber;   // '69599068'
echo $profile->name;        // 'Test Stichting Bolderbast'

// Get owner information
$owner = KVK::baseProfile('69599068')->owner();
echo $owner->legalForm;     // 'BesloteVennootschap'

// Get main branch details
$mainBranch = KVK::baseProfile('69599068')->mainBranch();
echo $mainBranch->branchNumber;  // '000037178598'

// Get all branches listing
$branches = KVK::baseProfile('69599068')->branches();
echo $branches->totalBranchCount;  // 1

// With geoData (adds GPS coordinates to addresses)
$owner = KVK::baseProfile('69599068')->geoData()->owner();
```

### Response shapes

#### BaseProfileResponse (from `get()`)

- `kvkNumber: string` — 8-digit KVK number
- `nonMailingIndicator: string` — 'Ja' or 'Nee'
- `name: string` — primary company name
- `formalRegistrationDate: ?string` — YYYYMMDD format
- `materialRegistration: ?MaterialRegistration` — with `startDate` and `endDate` properties
- `statutoryName: ?string` — statutory name (statutaire naam)
- `tradeNames: list<TradeName>` — each with `name` and `order`
- `sbiActivities: list<SbiActivity>` — each with `sbiCode`, `sbiDescription`, `mainActivityIndicator`
- `links: list<Link>` — HATEOAS links

#### BaseProfileOwnerResponse (from `owner()`)

- `rsin: ?string` — 9-digit RSIN number
- `legalForm: ?string` — e.g. 'BesloteVennootschap'
- `extendedLegalForm: ?string` — full legal form description
- `addresses: list<Address>` — structured addresses (with optional `geoData`)
- `websites: list<string>` — list of website URLs
- `links: list<Link>`

#### BaseProfileMainBranchResponse (from `mainBranch()`)

- `branchNumber: string` — 12-digit branch number
- `kvkNumber: string` — 8-digit KVK number
- `rsin: ?string` — 9-digit RSIN number
- `nonMailingIndicator: string` — 'Ja' or 'Nee'
- `firstTradeName: string` — primary trade name for this branch
- `mainBranchIndicator: string` — 'Ja' or 'Nee'
- `commercialBranchIndicator: string` — 'Ja' or 'Nee'
- `fullTimeEmployees: ?int`
- `totalEmployees: ?int`
- `partTimeEmployees: ?int`
- `tradeNames: list<TradeName>`
- `addresses: list<Address>`
- `websites: list<string>`
- `sbiActivities: list<SbiActivity>`
- `links: list<Link>`

#### BaseProfileBranchesResult (from `branches()`)

- `kvkNumber: string` — 8-digit KVK number
- `commercialBranchCount: int` — number of commercial branches
- `nonCommercialBranchCount: int` — number of non-commercial branches
- `totalBranchCount: int` — total number of branches
- `branches: BaseProfileBranchCollection` — typed collection of `BaseProfileBranchResponse` items
- `links: list<Link>`

Each `BaseProfileBranchResponse` in the collection contains:
- `branchNumber: string` — 12-digit branch number
- `firstTradeName: string` — primary trade name
- `mainBranchIndicator: string` — 'Ja' or 'Nee'
- `commercialBranchIndicator: string` — 'Ja' or 'Nee'
- `fullAddress: ?string` — flat address string
- `links: list<Link>`

### Faking Base Profile Calls

`KVK::fake()` covers all endpoints including the base profile. No separate configuration is required.

<!-- Faking Base Profile -->
```php
use DIJ\Kvk\Data\Responses\BaseProfileResponse;
use DIJ\Kvk\Facades\KVK;

// Default fake
KVK::fake();
$profile = KVK::baseProfile('69599068')->get();
// Returns BaseProfileResponse::fake() defaults

// Custom response
KVK::fake(
    baseProfile: BaseProfileResponse::fake(name: 'Custom Company'),
);
$profile = KVK::baseProfile('69599068')->get();
echo $profile->name; // 'Custom Company'
```

## Branch Profile API

Retrieve detailed branch information using a 12-digit branch number (`vestigingsnummer`).

### Usage

```php
use DIJ\Kvk\Facades\KVK;

// Get branch profile
$branch = KVK::branchProfile('000037178598')->get();
echo $branch->branchNumber;   // '000037178598'
echo $branch->kvkNumber;      // '68750110'
echo $branch->firstTradeName; // 'Test BV Donald'

// Include geo coordinates in address payloads
$branch = KVK::branchProfile('000037178598')->geoData()->get();
```

### Response shape

- `branchNumber: string` — 12-digit branch number
- `kvkNumber: string` — 8-digit KVK number
- `nonMailingIndicator: string` — 'Ja' or 'Nee'
- `firstTradeName: string`
- `mainBranchIndicator: string` — 'Ja' or 'Nee'
- `commercialBranchIndicator: string` — 'Ja' or 'Nee'
- `rsin: ?string`
- `formalRegistrationDate: ?string` — YYYYMMDD
- `materialRegistration: ?MaterialRegistration`
- `statutoryName: ?string`
- `fullTimeEmployees: ?int`
- `totalEmployees: ?int`
- `partTimeEmployees: ?int`
- `tradeNames: list<TradeName>`
- `addresses: list<Address>`
- `websites: list<string>`
- `sbiActivities: list<SbiActivity>`
- `links: list<Link>`

### Faking Branch Profile Calls

`KVK::fake()` also supports branch profile calls:

```php
use DIJ\Kvk\Data\Responses\BranchProfileResponse;
use DIJ\Kvk\Facades\KVK;

// Default fake
KVK::fake();
$branch = KVK::branchProfile('000037178598')->get();

// Custom response  
KVK::fake(
    branchProfile: BranchProfileResponse::fake(firstTradeName: 'Custom Branch'),
);
$branch = KVK::branchProfile('000037178598')->get();
echo $branch->firstTradeName; // 'Custom Branch'
```

## Naming API

Retrieve trade names using an 8-digit KVK number.

### Usage

```php
use DIJ\Kvk\Facades\KVK;

$naming = KVK::naming('69599068')->get();
echo $naming->kvkNumber;     // '69599068'
echo $naming->statutoryName; // 'Stichting Bolderbast'
echo $naming->name;          // 'Test Stichting Bolderbast'

foreach ($naming->branches as $branch) {
    echo $branch->branchNumber;
    echo $branch->firstTradeName; // commercial branches
    echo $branch->name;           // non-commercial branches
    echo $branch->alsoKnownAs;    // alias for non-commercial branches
}
```

### Response shape

- `kvkNumber: string` — 8-digit KVK number
- `statutoryName: string`
- `name: string`
- `rsin: ?string`
- `alsoKnownAs: ?string`
- `startDate: ?string` — YYYYMMDD
- `endDate: ?string` — YYYYMMDD, nullable
- `branches: NamingBranchCollection`
- `links: list<Link>`

Each `NamingBranchResponse` item contains:

- `branchNumber: string`
- `firstTradeName: ?string` — present for commercial branches
- `tradeNames: list<TradeName>` — present for commercial branches
- `name: ?string` — present for non-commercial branches
- `alsoKnownAs: ?string` — present for non-commercial branches
- `links: list<Link>`

### Faking Naming Calls

`KVK::fake()` also supports naming calls:

```php
use DIJ\Kvk\Data\Responses\NamingResponse;
use DIJ\Kvk\Facades\KVK;

// Default fake
KVK::fake();
$naming = KVK::naming('69599068')->get();

// Custom response
KVK::fake(
    naming: NamingResponse::fake(statutoryName: 'Custom Corp'),
);
$naming = KVK::naming('69599068')->get();
echo $naming->statutoryName; // 'Custom Corp'
```

## Verification

1. Run `php artisan tinker` and execute a search to verify credentials and connectivity
2. Check that `.env` has `KVK_API_KEY` set (the default `1234` is a placeholder and will return 401)
3. For sandbox testing, use base URL `https://api.kvk.nl/test` with test API key `l7xx1f2691f2520d487b902f4e0b57a0b197`

## Subscriptions API (Mutatieservice)

Monitor changes to KVK registry entries via the Mutatieservice. This API uses a scoped sub-object pattern: you first access the repository, then scope to a specific subscription to access signals.

### Usage

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

### Response shapes

#### SubscriptionsResult (from `get()`)

- `customerId: string` — customer ID linked to the API key
- `subscriptions: SubscriptionCollection` — typed collection of `SubscriptionResponse`

Each `SubscriptionResponse` contains:

- `id: string` — subscription identifier
- `contract: SubscriptionContract` — with `id` property
- `startDate: string` — ISO 8601 datetime
- `endDate: ?string` — ISO 8601 datetime, null when active
- `active: bool` — whether the subscription is currently active

#### SignalsResult (from `signals()`)

- `signals: SignalListItemCollection` — typed collection of `SignalListItem`
- `page: int` — current page number
- `resultsPerPage: int` — results per page
- `total: int` — total signal count
- `totalPages: int` — total number of pages
- `previous: ?string` — link to previous page
- `next: ?string` — link to next page

Each `SignalListItem` contains:

- `id: string` — signal identifier
- `timestamp: string` — ISO 8601 datetime
- `kvkNumber: string` — 8-digit KVK number
- `signalType: string` — signal type enum (e.g. `SignaalGewijzigdeInschrijving`)
- `branchNumber: ?string` — 12-digit branch number, if applicable

#### SignalResponse (from `signal()`)

- `messageId: string` — unique message identifier
- `signalType: string` — signal type enum
- `registrationId: string` — registration identifier
- `registrationTimestamp: string` — ISO 8601 datetime
- `relatesTo: array<string, mixed>` — polymorphic payload (shape depends on signal type)

### Signal types

| Signal type | Description |
|---|---|
| `SignaalGewijzigdeInschrijving` | Changed registration |
| `SignaalGewijzigdeVestiging` | Changed branch |
| `SignaalNieuweInschrijving` | New registration |
| `SignaalBeeindiging_2025_01` | Termination |
| `SignaalRechtsvormwijziging_2025_01` | Legal form change |
| `SignaalVoortzettingEnOverdracht_2025_01` | Continuation/transfer |
| `SignaalAdreswijziging_2025_01` | Address change |
| `SignaalNaamgeving_2025_01` | Name change |
| `SignaalFusieSplitsing_2025_01` | Merger/split |
| `SignaalActiviteitenWijziging_2025_01` | Activity change |

### Faking Subscription Calls

`KVK::fake()` also supports subscriptions and signals:

```php
use DIJ\Kvk\Data\Responses\SignalResponse;
use DIJ\Kvk\Data\Results\SignalsResult;
use DIJ\Kvk\Data\Results\SubscriptionsResult;
use DIJ\Kvk\Facades\KVK;

// Default fake
KVK::fake();
$subscriptions = KVK::subscriptions()->get();

// Custom responses
KVK::fake(
    subscriptions: SubscriptionsResult::fake(customerId: 'my-customer'),
    signals: SignalsResult::fake(total: 42),
    signal: SignalResponse::fake(messageId: 'custom-id'),
);
$subscriptions = KVK::subscriptions()->get();
echo $subscriptions->customerId; // 'my-customer'
```
