<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests\Data;

use DIJ\Kvk\Data\Responses\BranchProfileResponse;
use DIJ\Kvk\Data\ValueObjects\Address;
use DIJ\Kvk\Data\ValueObjects\MaterialRegistration;
use DIJ\Kvk\Data\ValueObjects\SbiActivity;
use DIJ\Kvk\Data\ValueObjects\TradeName;
use DIJ\Kvk\Exceptions\KvkException;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Http\Client\Response;
use PHPUnit\Framework\TestCase;

final class BranchProfileResponseTest extends TestCase
{
    public function test_from_array_maps_required_fields(): void
    {
        $data = [
            'vestigingsnummer' => '000037178598',
            'kvkNummer' => '68750110',
            'indNonMailing' => 'Nee',
            'eersteHandelsnaam' => 'Test BV Donald',
            'indHoofdvestiging' => 'Ja',
            'indCommercieleVestiging' => 'Ja',
        ];

        $response = BranchProfileResponse::fromArray($data);

        self::assertSame('000037178598', $response->branchNumber);
        self::assertSame('68750110', $response->kvkNumber);
        self::assertSame('Nee', $response->nonMailingIndicator);
        self::assertSame('Test BV Donald', $response->firstTradeName);
        self::assertSame('Ja', $response->mainBranchIndicator);
        self::assertSame('Ja', $response->commercialBranchIndicator);
        self::assertNull($response->rsin);
        self::assertNull($response->formalRegistrationDate);
        self::assertNull($response->materialRegistration);
        self::assertNull($response->statutoryName);
        self::assertNull($response->fullTimeEmployees);
        self::assertNull($response->totalEmployees);
        self::assertNull($response->partTimeEmployees);
        self::assertSame([], $response->tradeNames);
        self::assertSame([], $response->addresses);
        self::assertSame([], $response->websites);
        self::assertSame([], $response->sbiActivities);
        self::assertSame([], $response->links);
    }

    public function test_from_array_maps_all_fields(): void
    {
        $data = [
            'vestigingsnummer' => '000037178598',
            'kvkNummer' => '68750110',
            'indNonMailing' => 'Nee',
            'eersteHandelsnaam' => 'Test BV Donald',
            'indHoofdvestiging' => 'Ja',
            'indCommercieleVestiging' => 'Ja',
            'rsin' => '123456789',
            'formeleRegistratiedatum' => '20150622',
            'materieleRegistratie' => ['datumAanvang' => '20150101', 'datumEinde' => null],
            'statutaireNaam' => 'Test BV Donald',
            'voltijdWerkzamePersonen' => 10,
            'totaalWerkzamePersonen' => 15,
            'deeltijdWerkzamePersonen' => 5,
            'handelsnamen' => [['naam' => 'Test BV Donald', 'volgorde' => 1]],
            'adressen' => [['type' => 'bezoekadres', 'straatnaam' => 'Watermolenlaan', 'huisnummer' => 1]],
            'websites' => ['https://example.com'],
            'sbiActiviteiten' => [['sbiCode' => '86101', 'sbiOmschrijving' => 'Universitair medisch centra', 'indHoofdactiviteit' => 'Ja']],
            'links' => [['rel' => 'basisprofiel', 'href' => 'https://api.kvk.nl/api/v1/basisprofielen/68750110']],
        ];

        $response = BranchProfileResponse::fromArray($data);

        self::assertSame('123456789', $response->rsin);
        self::assertSame('20150622', $response->formalRegistrationDate);
        self::assertInstanceOf(MaterialRegistration::class, $response->materialRegistration);
        self::assertSame('Test BV Donald', $response->statutoryName);
        self::assertSame(10, $response->fullTimeEmployees);
        self::assertSame(15, $response->totalEmployees);
        self::assertSame(5, $response->partTimeEmployees);
        self::assertCount(1, $response->tradeNames);
        self::assertInstanceOf(TradeName::class, $response->tradeNames[0]);
        self::assertCount(1, $response->addresses);
        self::assertInstanceOf(Address::class, $response->addresses[0]);
        self::assertCount(1, $response->websites);
        self::assertSame('https://example.com', $response->websites[0]);
        self::assertCount(1, $response->sbiActivities);
        self::assertInstanceOf(SbiActivity::class, $response->sbiActivities[0]);
        self::assertCount(1, $response->links);
    }

    public function test_from_response_parses_valid_json(): void
    {
        $response = new Response(
            new Psr7Response(200, ['Content-Type' => 'application/json'], json_encode([
                'vestigingsnummer' => '000037178598',
                'kvkNummer' => '68750110',
                'indNonMailing' => 'Nee',
                'eersteHandelsnaam' => 'Test BV Donald',
                'indHoofdvestiging' => 'Ja',
                'indCommercieleVestiging' => 'Ja',
            ], JSON_THROW_ON_ERROR)),
        );

        $result = BranchProfileResponse::fromResponse($response);

        self::assertInstanceOf(BranchProfileResponse::class, $result);
        self::assertSame('000037178598', $result->branchNumber);
    }

    public function test_from_response_throws_on_invalid_body(): void
    {
        $response = new Response(
            new Psr7Response(200, ['Content-Type' => 'application/json'], json_encode('not-an-array', JSON_THROW_ON_ERROR)),
        );

        $this->expectException(KvkException::class);
        BranchProfileResponse::fromResponse($response);
    }

    public function test_fake_returns_correct_defaults(): void
    {
        $response = BranchProfileResponse::fake();

        self::assertSame('000037178598', $response->branchNumber);
        self::assertSame('68750110', $response->kvkNumber);
        self::assertSame('Nee', $response->nonMailingIndicator);
        self::assertSame('Test BV Donald', $response->firstTradeName);
        self::assertSame('Ja', $response->mainBranchIndicator);
        self::assertSame('Ja', $response->commercialBranchIndicator);
        self::assertSame('123456789', $response->rsin);
        self::assertSame('20150622', $response->formalRegistrationDate);
        self::assertInstanceOf(MaterialRegistration::class, $response->materialRegistration);
        self::assertSame('Test BV Donald', $response->statutoryName);
        self::assertSame(10, $response->fullTimeEmployees);
        self::assertSame(15, $response->totalEmployees);
        self::assertSame(5, $response->partTimeEmployees);
        self::assertCount(1, $response->tradeNames);
        self::assertCount(1, $response->addresses);
        self::assertCount(1, $response->websites);
        self::assertCount(1, $response->sbiActivities);
    }
}
