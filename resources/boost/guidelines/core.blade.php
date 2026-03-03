## KVK API (`dij-digital/kvk-laravel`)

This project uses the KVK (Kamer van Koophandel) API package to search the Dutch trade register (Handelsregister).

### Key Facts

- Entry point: `DIJ\Kvk\Facades\KVK` facade — methods: `search()`, `baseProfile(string $kvkNumber)`, `branchProfile(string $branchNumber)`, `naming(string $kvkNumber)`, `subscriptions()`
- Config: `config/kvk.php` with `KVK_API_KEY` and `KVK_BASE_URL` env vars
- All API responses use Dutch field names internally; the package translates them to English DTO properties
- Throws typed exceptions: `KvkAuthenticationException` (401/403), `KvkServerException` (500+), `KvkRequestException` (other) — all extend `KvkException` with `->statusCode` and `->responseBody`

### Quick Example

@verbatim
<code-snippet name="Search and query the KVK registry" lang="php">
use DIJ\Kvk\Facades\KVK;

// Search the KVK registry
$result = KVK::search()->name('Acme')->city('Amsterdam')->get();
foreach ($result->items as $company) {
    echo $company->kvkNumber; // '69599068'
    echo $company->name;      // 'Test BV Donald'
}

// Get detailed company profile
$profile = KVK::baseProfile('69599068')->get();
echo $profile->name;          // 'Test Stichting Bolderbast'
echo $profile->statutoryName; // 'Stichting Bolderbast'

// Get detailed branch profile
$branch = KVK::branchProfile('000037178598')->get();
echo $branch->firstTradeName; // 'Test BV Donald'

// Get naming profile (trade names)
$naming = KVK::naming('69599068')->get();
echo $naming->statutoryName; // 'Stichting Bolderbast'

// List subscriptions and signals (change notifications)
$subscriptions = KVK::subscriptions()->get();
$signals = KVK::subscriptions()->subscription('sub-id')->signals();
</code-snippet>
@endverbatim

### Testing

Use `KVK::fake()` to mock the API in tests. Pass named parameters to customize what each endpoint returns:

@verbatim
<code-snippet name="Faking KVK API calls in tests" lang="php">
use DIJ\Kvk\Data\Responses\BaseProfileResponse;
use DIJ\Kvk\Data\ValueObjects\SbiActivity;
use DIJ\Kvk\Facades\KVK;

// Zero-arg: all endpoints return sensible defaults
KVK::fake();

// Customize specific endpoints with named parameters
KVK::fake(
    baseProfile: BaseProfileResponse::fake(
        sbiActivities: [SbiActivity::fake(sbiCode: '86101')],
    ),
);
$profile = KVK::baseProfile('69599068')->get();
echo $profile->sbiActivities[0]->sbiCode; // '86101'
</code-snippet>
@endverbatim

For advanced HTTP-level testing with full request/response verification, use `Http::fake()` with Dutch field names (`kvkNummer`, `naam`, `actief`).

For detailed usage patterns, parameter reference, and response shapes, use the `kvk-api-development` skill.
