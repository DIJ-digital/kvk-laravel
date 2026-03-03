---
name: kvk-api
description: Complete KVK (Kamer van Koophandel) Handelsregister API reference — endpoints, parameters, response shapes, test data, error codes, and Dutch-to-English field mapping. Use when implementing or modifying API endpoints in this package.
---

## When to use this skill

Load this skill when you need to:

- Implement a new KVK API endpoint (repository, DTOs, collection)
- Understand KVK API response shapes to build or modify DTOs
- Map Dutch API field names to English DTO property names
- Write tests with realistic fixture data
- Debug API response parsing issues
- Understand KVK error codes and status codes

## API Overview

The KVK Handelsregister has 5 APIs. All are GET-only, return JSON, and use `apikey` header authentication.

| API | Endpoint | Lookup by | Purpose |
|---|---|---|---|
| Zoeken | `GET /api/v2/zoeken` | Query params | Search the registry — returns KVK numbers and basic info |
| Basisprofiel | `GET /api/v1/basisprofielen/{kvkNummer}` | KVK number (8 digits) | Company profile: owner, main branch, SBI activities |
| Vestigingsprofiel | `GET /api/v1/vestigingsprofielen/{vestigingsnummer}` | Branch number (12 digits) | Branch details: address, employees, activities |
| Naming | `GET /api/v1/naamgevingen/kvknummer/{kvkNummer}` | KVK number (8 digits) | Trade names: statutory name, trade names per branch |
| Mutatieservice | `GET /api/v1/abonnementen` | Subscription ID | Change notifications: signals when registry data changes |

Base URL production: `https://api.kvk.nl`
Base URL test: `https://api.kvk.nl/test`

Authentication: `apikey` HTTP header.

## Zoeken API (Search)

### Endpoint

```
GET /api/v2/zoeken
```

### Input parameters

| Parameter | Type | Dutch API name | Details |
|---|---|---|---|
| kvkNumber | string | `kvkNummer` | 8-digit KVK number |
| RSIN | string | `rsin` | 9-digit RSIN number |
| branchNumber | string | `vestigingsnummer` | 12-digit branch number |
| name | string | `naam` | Trade name or statutory name (exact word matching, not prefix) |
| streetName | string | `straatnaam` | Street name |
| houseNumber | integer | `huisnummer` | Only with postcode |
| houseLetter | string | `huisletter` | Only with houseNumber, single letter |
| postalCode | string | `postcode` | Only with houseNumber or poBoxNumber |
| poBoxNumber | integer | `postbusnummer` | Only with postcode |
| city | string | `plaats` | City name |
| type | string | `type` | Filter: `hoofdvestiging`, `nevenvestiging`, `rechtspersoon`. Combine with multiple `&type=` params |
| includeInactiveRegistrations | boolean | `inclusiefInactieveRegistraties` | Default: false |
| page | integer | `pagina` | 1–1000 |
| resultsPerPage | integer | `resultatenPerPagina` | Default: 10, max: 100 |

### Output shape

```json
{
  "pagina": 1,
  "resultatenPerPagina": 10,
  "totaal": 1,
  "vorige": "string (link to previous page, if available)",
  "volgende": "string (link to next page, if available)",
  "resultaten": [
    {
      "kvkNummer": "69599068",
      "rsin": "string (only when rsin was input)",
      "vestigingsnummer": "000037178598",
      "naam": "Test BV Donald",
      "adres": {
        "binnenlandsAdres": {
          "type": "bezoekadres",
          "straatnaam": "Hizzaarderlaan",
          "huisnummer": 1,
          "huisletter": "A",
          "postbusnummer": 123,
          "postcode": "1234AB",
          "plaats": "Lollum"
        },
        "buitenlandsAdres": {
          "straatHuisnummer": "123 Main Street",
          "postcodeWoonplaats": "12345 Berlin",
          "land": "Duitsland"
        }
      },
      "type": "hoofdvestiging",
      "actief": "Ja",
      "vervallenNaam": "string (expired trade name that matched search)",
      "links": [
        {
          "rel": "basisprofiel",
          "href": "https://api.kvk.nl/api/v1/basisprofielen/69599068"
        },
        {
          "rel": "vestigingsprofiel",
          "href": "https://api.kvk.nl/api/v1/vestigingsprofielen/000037178598"
        }
      ]
    }
  ],
  "links": [
    {
      "rel": "self",
      "href": "https://api.kvk.nl/api/v2/zoeken?kvknummer=69599068&pagina=1&resultatenperpagina=10"
    }
  ]
}
```

### Search result types

Results have a `type` field with one of:
- `hoofdvestiging` — main branch (has both basisprofiel and vestigingsprofiel links)
- `nevenvestiging` — secondary branch (has both links)
- `rechtspersoon` — legal entity (has basisprofiel link only, no vestigingsnummer)

### Search behavior

- Exact word matching only — `koophand` returns 0 results, `koophandel` works
- Inactive registrations excluded by default
- Max 100 results per page
- No guaranteed ordering, but preference: hoofdvestiging → nevenvestiging → rechtspersoon
- Links are absent when deregistered before 2010-05-22 and `actief` is `false`

## Basisprofiel API (Company Profile)

### Endpoints

```
GET /api/v1/basisprofielen/{kvkNummer}
GET /api/v1/basisprofielen/{kvkNummer}/eigenaar
GET /api/v1/basisprofielen/{kvkNummer}/hoofdvestiging
GET /api/v1/basisprofielen/{kvkNummer}/vestigingen
```

### Input parameters

| Parameter | Type | Dutch API name | Details |
|---|---|---|---|
| kvkNumber | string | `kvkNummer` | 8-digit KVK number (path parameter) |
| geoData | boolean | `geoData` | Include geo coordinates. Default: false |

### Output — Main profile

```json
{
  "kvkNummer": "69599068",
  "indNonMailing": "Ja",
  "naam": "Test Stichting Bolderbast",
  "formeleRegistratiedatum": "20150622",
  "materieleRegistratie": {
    "datumAanvang": "20150101",
    "datumEinde": "20201231"
  },
  "statutaireNaam": "Stichting Bolderbast",
  "handelsnamen": [
    { "naam": "Test Stichting Bolderbast", "volgorde": 1 }
  ],
  "sbiActiviteiten": [
    {
      "sbiCode": "86101",
      "sbiOmschrijving": "Universitair medisch centra",
      "indHoofdactiviteit": "Ja"
    }
  ],
  "links": []
}
```

### Output — Eigenaar (Owner)

```json
{
  "rsin": "123456789",
  "rechtsvorm": "BesloteVennootschap",
  "uitgebreideRechtsvorm": "Besloten Vennootschap met gewone structuur",
  "adressen": [],
  "websites": ["https://example.com"],
  "links": []
}
```

### Output — Hoofdvestiging (Main Branch)

```json
{
  "vestigingsnummer": "000037178598",
  "kvkNummer": "69599068",
  "rsin": "123456789",
  "indNonMailing": "Nee",
  "formeleRegistratiedatum": "20150622",
  "materieleRegistratie": {
    "datumAanvang": "20150101",
    "datumEinde": null
  },
  "eersteHandelsnaam": "Test BV Donald",
  "indHoofdvestiging": "Ja",
  "indCommercieleVestiging": "Ja",
  "voltijdWerkzamePersonen": 10,
  "totaalWerkzamePersonen": 15,
  "deeltijdWerkzamePersonen": 5,
  "handelsnamen": [],
  "adressen": [],
  "websites": [],
  "sbiActiviteiten": [],
  "links": []
}
```

### Output — Vestigingen (Branch listing)

```json
{
  "kvkNummer": "69599068",
  "aantalCommercieleVestigingen": 3,
  "aantalNietCommercieleVestigingen": 1,
  "totaalAantalVestigingen": 4,
  "vestigingen": [
    {
      "vestigingsnummer": "000037178598",
      "eersteHandelsnaam": "Test BV Donald",
      "indHoofdvestiging": "Ja",
      "indCommercieleVestiging": "Ja",
      "volledigAdres": "Hizzaarderlaan 1 1234AB Lollum",
      "links": []
    }
  ],
  "links": []
}
```

## Vestigingsprofiel API (Branch Profile)

### Endpoint

```
GET /api/v1/vestigingsprofielen/{vestigingsnummer}
```

### Input parameters

| Parameter | Type | Dutch API name | Details |
|---|---|---|---|
| branchNumber | string | `vestigingsnummer` | 12-digit branch number (path parameter) |
| geoData | boolean | `geoData` | Include geo coordinates. Default: false |

### Output shape

```json
{
  "vestigingsnummer": "000037178598",
  "kvkNummer": "68750110",
  "rsin": "123456789",
  "indNonMailing": "Nee",
  "formeleRegistratiedatum": "20150622",
  "materieleRegistratie": {
    "datumAanvang": "20150101",
    "datumEinde": null
  },
  "statutaireNaam": "Test BV Donald",
  "eersteHandelsnaam": "Test BV Donald",
  "indHoofdvestiging": "Ja",
  "indCommercieleVestiging": "Ja",
  "voltijdWerkzamePersonen": 10,
  "totaalWerkzamePersonen": 15,
  "deeltijdWerkzamePersonen": 5,
  "handelsnamen": [
    { "naam": "Test BV Donald", "volgorde": 1 }
  ],
  "adressen": [],
  "websites": ["https://example.com"],
  "sbiActiviteiten": [
    {
      "sbiCode": "86101",
      "sbiOmschrijving": "Universitair medisch centra",
      "indHoofdactiviteit": "Ja"
    }
  ],
  "links": []
}
```

## Naming API (Trade Names)

### Endpoint

```
GET /api/v1/naamgevingen/kvknummer/{kvkNummer}
```

### Input parameters

| Parameter | Type | Dutch API name | Details |
|---|---|---|---|
| kvkNumber | string | `kvkNummer` | 8-digit KVK number (path parameter) |

### Output shape

```json
{
  "kvkNummer": "69599068",
  "rsin": "123456789",
  "statutaireNaam": "Stichting Bolderbast",
  "naam": "Test Stichting Bolderbast",
  "ookGenoemd": "Bolderbast",
  "startdatum": "20150101",
  "einddatum": null,
  "vestigingen": [
    {
      "vestigingsnummer": "000037178598",
      "eersteHandelsnaam": "Test Stichting Bolderbast",
      "handelsnamen": [
        { "naam": "Test Stichting Bolderbast", "volgorde": 1 }
      ],
      "links": []
    }
  ],
  "links": []
}
```

### Non-commercial branch shape

Non-commercial branches use `naam` and `ookGenoemd` instead of `eersteHandelsnaam` and `handelsnamen`:

```json
{
  "vestigingsnummer": "000037178598",
  "naam": "Stichting Branch",
  "ookGenoemd": "Branch Alias",
  "links": []
}
```
## Mutatieservice API (Change Notifications)

The Mutatieservice is structurally different from other KVK APIs. It uses nested resources (subscriptions → signals) and requires a **scoped sub-object pattern** instead of the flat repository pattern.

### Endpoints

```
GET /api/v1/abonnementen                                        → List subscriptions
GET /api/v1/abonnementen/{abonnementId}                         → List signals for subscription
GET /api/v1/abonnementen/{abonnementId}/signalen/{signaalId}    → Get specific signal
```

### Input parameters

**List subscriptions** — no parameters.

**List signals:**

| Parameter | Type | Dutch API name | Details |
|---|---|---|---|
| subscriptionId | string | `abonnementId` | Subscription ID (path parameter) |
| from | datetime | `vanaf` | Filter signals from this datetime |
| to | datetime | `tot` | Filter signals until this datetime |
| page | integer | `pagina` | Page number, starts at 1 |
| resultsPerPage | integer | `aantal` | Results per page. Min: 10, max: 500, default: 100 |

**Get specific signal:**

| Parameter | Type | Dutch API name | Details |
|---|---|---|---|
| subscriptionId | string | `abonnementId` | Subscription ID (path parameter) |
| signalId | string | `signaalId` | Signal ID (path parameter) |

### Output — Subscriptions list

```json
{
  "klantId": "customer-123",
  "abonnementen": [
    {
      "id": "subscription-456",
      "contract": { "id": "contract-789" },
      "startDatum": "2024-01-01T00:00:00Z",
      "eindDatum": null,
      "actief": true
    }
  ]
}
```

### Output — Signals list (paginated)

```json
{
  "pagina": 1,
  "aantal": 100,
  "totaal": 250,
  "totaalPaginas": 3,
  "volgende": "string (link to next page)",
  "vorige": "string (link to previous page)",
  "signalen": [
    {
      "id": "signal-001",
      "timestamp": "2024-05-14T15:25:13.773Z",
      "kvknummer": "69792917",
      "signaalType": "SignaalGewijzigdeInschrijving",
      "vestigingsnummer": "000038821281"
    }
  ]
}
```

### Output — Individual signal

Signals are polymorphic — the `signaalType` field determines the shape. All signals share a base structure:

```json
{
  "signaal": {
    "berichtId": "3e96fad5-606e-43be-9bd5-4718f8afd273",
    "signaalType": "SignaalGewijzigdeInschrijving",
    "registratieId": "-64945f8e:18f77b51fa3:-4654",
    "registratieTijdstip": "2024-05-14T15:25:13.773Z",
    "heeftBetrekkingOp": {
      "kvkNummer": "69792917",
      "nonMailing": true,
      "totaalWerkzamePersonen": 12,
      "heeftAlsEigenaar": { ... },
      "wordtUitgeoefendIn": [ ... ]
    }
  }
}
```

### Signal types

| Signal type | Description |
|---|---|
| `SignaalGewijzigdeInschrijving` | Changed registration — flags which data categories changed |
| `SignaalGewijzigdeVestiging` | Changed branch — flags which branch data changed |
| `SignaalNieuweInschrijving` | New registration — includes start date and reason |
| `SignaalBeeindiging_2025_01` | Termination — includes end date and reason |
| `SignaalRechtsvormwijziging_2025_01` | Legal form change — includes old legal form |
| `SignaalVoortzettingEnOverdracht_2025_01` | Continuation/transfer — includes involved parties |
| `SignaalAdreswijziging_2025_01` | Address change — includes affected branches |
| `SignaalNaamgeving_2025_01` | Name change — includes old/new names |
| `SignaalFusieSplitsing_2025_01` | Merger/split — includes involved companies |
| `SignaalActiviteitenWijziging_2025_01` | Activity change — includes SBI code changes |


## Shared Data Structures

### Address (Adres)

Used in Basisprofiel (hoofdvestiging/eigenaar) and Vestigingsprofiel responses:

```json
{
  "type": "bezoekadres",
  "indicatieAfgeschermd": "Nee",
  "volledigAdres": "Watermolenlaan 1 3447GT Woerden",
  "straatnaam": "Watermolenlaan",
  "huisnummer": 1,
  "huisnummerToevoeging": "bis",
  "huisletter": "A",
  "toevoegingAdres": "3e etage",
  "postcode": "3447GT",
  "postbusnummer": 123,
  "plaats": "Woerden",
  "straatHuisnummer": "Watermolenlaan 1",
  "postcodeWoonplaats": "3447GT Woerden",
  "regio": "Utrecht",
  "land": "Nederland",
  "geoData": {
    "addresseerbaarObjectId": "0632010000010090",
    "nummerAanduidingId": "0632200000010090",
    "gpsLatitude": 52.08151653230184,
    "gpsLongitude": 4.890048011859921,
    "rijksdriehoekX": 120921.45,
    "rijksdriehoekY": 454921.47,
    "rijksdriehoekZ": 0.0
  }
}
```

Address `type` values: `bezoekadres` (visiting), `correspondentieadres` (postal).
`geoData` only present when `?geoData=true` query parameter is set.

### Registration date format

Dates in the API can contain zeros for unknown parts:
- `20150622` — full date known
- `20150600` — day unknown
- `20150000` — month unknown
- `00000000` — date entirely unknown

Only numeric values. Format: `YYYYMMDD`.

### MaterieleRegistratie

```json
{
  "datumAanvang": "20150101",
  "datumEinde": "20201231"
}
```

`datumEinde` is `null` when the registration is still active.

### SBI Activity

```json
{
  "sbiCode": "86101",
  "sbiOmschrijving": "Universitair medisch centra",
  "indHoofdactiviteit": "Ja"
}
```

SBI = Standard Industrial Classification (Dutch: Standaard Bedrijfsindeling). Reference: https://www.kvk.nl/overzicht-standaard-bedrijfsindeling/

### Handelsnaam (Trade name)

```json
{
  "naam": "Test BV Donald",
  "volgorde": 1
}
```

Ordered by registration order.

### HATEOAS Links

```json
{
  "rel": "basisprofiel",
  "href": "https://api.kvk.nl/api/v1/basisprofielen/69599068"
}
```

The API follows HAL-style HATEOAS. All responses include a `links` array with navigational references to related resources.

## Error Handling

### HTTP Status Codes

| Code | Meaning |
|---|---|
| 400 | Bad request — invalid parameters |
| 401 | Not authenticated — missing or invalid API key |
| 404 | Not found — no results or invalid resource |
| 500 | Internal server error |

### KVK-specific IPD Error Codes

| Code | HTTP | Description |
|---|---|---|
| IPD0001 | 404 | Requested product does not exist |
| IPD0004 | 400 | KVK number is invalid |
| IPD0005 | 404 | Cannot deliver product for this KVK number |
| IPD0006 | 400 | Branch number (vestigingsnummer) is invalid |
| IPD0007 | 404 | Cannot deliver product for this branch number |
| IPD0010 | 400 | RSIN is invalid |
| IPD1002 | 404 | Data temporarily unavailable (being processed) |
| IPD1003 | 404 | Data temporarily unavailable (retry in 5 minutes) |
| IPD1998 | 400 | General input parameter error |
| IPD1999 | 400 | Specified parameter(s) invalid |
| IPD5200 | 404 | No results found for search parameters |
| IPD5203 | 404 | Specified type is invalid |
| IPD9999 | 500 | General technical error |

## Test Environment

### Test base URL

```
https://api.kvk.nl/test
```

### Test API key

```
l7xx1f2691f2520d487b902f4e0b57a0b197
```

Header: `apikey: l7xx1f2691f2520d487b902f4e0b57a0b197`

### Test KVK numbers

| KVK Number | Legal form |
|---|---|
| 69599084 | Eenmanszaak (sole proprietorship) |
| 68727720 | NV (public limited company) |
| 90004760 | NV |
| 68750110 | BV (private limited company) |
| 90001354 | BV |
| 69599068 | Stichting (foundation) |
| 90000102 | Stichting |
| 90006623 | Stichting |
| 69599076 | VoF (general partnership) |
| 90005414 | VoF |
| 55344526 | Cooperatie (cooperative) |
| 90002520 | Kerkgenootschap (church community) |
| 90002490 | Onderlinge Waarborg Maatschappij (mutual insurance) |
| 90001745 | Maatschap (professional partnership) |
| 90003942 | Commanditaire Vennootschap (limited partnership) |
| 55505201 | Overige Privaatrechtelijke Rechtspersoon |
| 90000749 | Vereniging van Eigenaars (owners association) |
| 90004973 | Error response test case |
| 90002903 | Error message test case |

### Test branch numbers (Vestigingsprofiel)

| Branch Number | Legal form |
|---|---|
| 38509504 | Eenmanszaak |
| 38509520 | Eenmanszaak |
| 37178598 | BV |
| 37178601 | BV |
| 990000541921 | BV |
| 37143557 | NV |
| 990064773193 | Stichting (with geoData) |
| 990064773207 | Stichting (with geoData) |
| 38509474 | VoF |
| 38509490 | VoF |
| 990000216645 | VoF |
| 990000821206 | VoF |
| 37178605 | Cooperatie |
| 990000246858 | Onderlinge Waarborg Maatschappij |
| 990000637800 | Maatschap |
| 990000008288 | Commanditaire Vennootschap |
| 990000246530 | Commanditaire Vennootschap |
| 990000768218 | Commanditaire Vennootschap |
| 990000852070 | Commanditaire Vennootschap |

### Example test calls

```bash
# Search
curl "https://api.kvk.nl/test/api/v2/zoeken?naam=test" -H "apikey: l7xx1f2691f2520d487b902f4e0b57a0b197"

# Basisprofiel
curl "https://api.kvk.nl/test/api/v1/basisprofielen/69599068" -H "apikey: l7xx1f2691f2520d487b902f4e0b57a0b197"

# Basisprofiel sub-resources
curl "https://api.kvk.nl/test/api/v1/basisprofielen/69599068/eigenaar" -H "apikey: l7xx1f2691f2520d487b902f4e0b57a0b197"
curl "https://api.kvk.nl/test/api/v1/basisprofielen/69599068/hoofdvestiging" -H "apikey: l7xx1f2691f2520d487b902f4e0b57a0b197"
curl "https://api.kvk.nl/test/api/v1/basisprofielen/69599068/vestigingen" -H "apikey: l7xx1f2691f2520d487b902f4e0b57a0b197"

# Vestigingsprofiel
curl "https://api.kvk.nl/test/api/v1/vestigingsprofielen/37178598" -H "apikey: l7xx1f2691f2520d487b902f4e0b57a0b197"

# Naming
curl "https://api.kvk.nl/test/api/v1/naamgevingen/kvknummer/69599068" -H "apikey: l7xx1f2691f2520d487b902f4e0b57a0b197"
# Mutatieservice
curl "https://api.kvk.nl/test/api/v1/abonnementen" -H "apikey: l7xx1f2691f2520d487b902f4e0b57a0b197"

```

## Dutch → English Field Mapping

When creating DTOs, use English property names. The `toArray()` method maps to Dutch API names.

### Common field mapping

| English property | Dutch API field | Type |
|---|---|---|
| kvkNumber | kvkNummer | string (8 digits) |
| rsin / RSIN | rsin | string (9 digits) |
| branchNumber | vestigingsnummer | string (12 digits) |
| name | naam | string |
| streetName | straatnaam | string |
| houseNumber | huisnummer | integer |
| houseLetter | huisletter | string |
| houseNumberAddition | huisnummerToevoeging | string |
| postalCode | postcode | string |
| poBoxNumber | postbusnummer | integer |
| city | plaats | string |
| country | land | string |
| region | regio | string |
| type | type | string |
| active | actief | string (Ja/Nee) |
| expiredName | vervallenNaam | string |
| includeInactiveRegistrations | inclusiefInactieveRegistraties | boolean |
| page | pagina | integer |
| resultsPerPage | resultatenPerPagina | integer |
| total | totaal | integer |
| previous | vorige | string |
| next | volgende | string |
| results | resultaten | array |
| nonMailingIndicator | indNonMailing | string (Ja/Nee) |
| formalRegistrationDate | formeleRegistratiedatum | string (YYYYMMDD) |
| materialRegistration | materieleRegistratie | object |
| startDate | datumAanvang | string (YYYYMMDD) |
| endDate | datumEinde | string (YYYYMMDD, nullable) |
| statutoryName | statutaireNaam | string |
| firstTradeName | eersteHandelsnaam | string |
| tradeNames | handelsnamen | array |
| mainBranchIndicator | indHoofdvestiging | string (Ja/Nee) |
| commercialBranchIndicator | indCommercieleVestiging | string (Ja/Nee) |
| fullTimeEmployees | voltijdWerkzamePersonen | integer |
| totalEmployees | totaalWerkzamePersonen | integer |
| partTimeEmployees | deeltijdWerkzamePersonen | integer |
| addresses | adressen | array |
| websites | websites | array |
| sbiActivities | sbiActiviteiten | array |
| sbiCode | sbiCode | string |
| sbiDescription | sbiOmschrijving | string |
| mainActivityIndicator | indHoofdactiviteit | string (Ja/Nee) |
| legalForm | rechtsvorm | string |
| extendedLegalForm | uitgebreideRechtsvorm | string |
| fullAddress | volledigAdres | string |
| addressAddition | toevoegingAdres | string |
| shielded | indicatieAfgeschermd | string (Ja/Nee) |
| streetHouseNumber | straatHuisnummer | string |
| postalCodeCity | postcodeWoonplaats | string |
| alsoKnownAs | ookGenoemd | string |
| commercialBranchCount | aantalCommercieleVestigingen | integer |
| nonCommercialBranchCount | aantalNietCommercieleVestigingen | integer |
| totalBranchCount | totaalAantalVestigingen | integer |
| branches | vestigingen | array |
| order | volgorde | integer |
| subscriptionId | abonnementId | string |
| signalId | signaalId | string |
| customerId | klantId | string |
| contractId | contract.id | string |
| startDate | startDatum | datetime (ISO 8601) |
| endDate | eindDatum | datetime (ISO 8601, nullable) |
| active | actief | boolean |
| signalType | signaalType | string (enum) |
| messageId | berichtId | string |
| registrationId | registratieId | string |
| registrationTimestamp | registratieTijdstip | datetime (ISO 8601) |
| relatesTo | heeftBetrekkingOp | object |
| totalPages | totaalPaginas | integer |

## Implementation Checklist

When adding a new API endpoint to this package, follow AGENTS.md "Adding a New API Endpoint" and use the data structures above to build correct DTOs. Every Dutch field in the API response needs a corresponding English-named property on the DTO, with `fromArray()` handling the mapping.

## Repository Design Patterns

When implementing repositories, the pattern depends on the API structure:

### Pattern A: Flat repository (Search, Vestigingsprofiel, Naming, Basisprofiel)

One repository class with fluent setters for optional parameters and terminal methods for each endpoint.

- **Required identifiers** → constructor parameter (passed via `KVK::basisprofiel($kvkNumber)`)
- **Required identifiers** → constructor parameter (passed via methods like `KVK::baseProfile($kvkNumber)` and `KVK::branchProfile($branchNumber)`)
- **Optional filters** → fluent setter methods returning `self`
- **Endpoints** → terminal methods (`get()`, `eigenaar()`, `hoofdvestiging()`, etc.)

### Pattern B: Scoped sub-object (Mutatieservice)

When an API has nested resources where a parent identifier is required for child endpoints but not for the parent listing, use a scoped sub-object:

- `SubscriptionRepository` — has `get()` terminal (lists subscriptions, no ID needed) and `subscription(string $id)` which returns a `SubscriptionScope`
- `SubscriptionScope` — has the subscription ID baked in, fluent setters for filtering, and `signals()`/`signal(string $id)` terminals

This ensures PHPStan level 10 type safety: you cannot call `signals()` without first providing a subscription ID, because the method only exists on the scoped class.



## Testing

The package provides a complete fake/mock infrastructure for testing without HTTP calls.

### KVK::fake() — Facade fake (primary approach)

Swap the entire KVK binding via `Facade::swap()`. No HTTP calls are made.

```php
use DIJ\Kvk\Facades\KVK;
use DIJ\Kvk\Data\SearchResponse;

// Empty result (0 items)
KVK::fake();

// With specific responses
KVK::fake(
    SearchResponse::fake(kvkNumber: '69599068', name: 'Acme BV'),
    SearchResponse::fake(kvkNumber: '12345678', name: 'Other BV'),
);
```

The `FakeKVK` class builds a `SearchResult` with `page=1`, `resultsPerPage=10`, and `total=count($responses)` and exposes endpoint fakes like `baseProfile()` and `branchProfile()`.

### SearchResponse::fake() — Factory with defaults

Named parameters with sensible defaults. Only specify fields relevant to your test.

```php
SearchResponse::fake(
    kvkNumber: '69599068',
    name: 'Test BV Donald',
    type: 'hoofdvestiging',
    active: 'Ja',
    rsin: null,
    branchNumber: '000037178598',
    address: null,
    expiredName: null,
    links: [],
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
| `expiredName` | `null` |
| `links` | `[]` |

Defaults come from KVK test data.

### FakeSearchRepository

`FakeSearchRepository` is a standalone class in `src/Testing/` (NOT extending `SearchRepository`). It provides:

- **14 no-op fluent setters** matching real `SearchRepository` signatures: `kvkNumber()`, `rsin()`, `branchNumber()`, `name()`, `streetName()`, `city()`, `postalCode()`, `houseNumber()`, `houseLetter()`, `poBoxNumber()`, `type()`, `includeInactiveRegistrations()`, `page()`, `resultsPerPage()`
- **Terminal methods** `get()` and `search(SearchParameters)` that return the pre-configured `SearchResult`
- All fluent methods return `$this` but don't filter — they exist for interface compatibility only

### FakeKVK

`FakeKVK` is a standalone class in `src/Testing/` with:

- **Constructor**: `SearchResponse ...$responses` (variadic)
- **`search()` method**: Returns a `FakeSearchRepository` pre-loaded with the given responses
- **Result building**: Constructs `SearchResult` with `page=1`, `resultsPerPage=10`, `total=count($responses)`

### Facade cleanup

For plain PHPUnit tests (not extending Laravel's `TestCase`), add this to your test class:

```php
protected function tearDown(): void
{
    \Illuminate\Support\Facades\Facade::clearResolvedInstances();
    parent::tearDown();
}
```

Laravel's `TestCase` handles cleanup automatically.

### Http::fake() — Advanced alternative

For lower-level tests that verify the full HTTP pipeline (request parameters and response parsing), use `Http::fake()` directly:

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
```

**Important**: Responses must use Dutch API field names (`kvkNummer`, `naam`, `actief`, etc.). See the Dutch→English field mapping table above for the complete mapping.
