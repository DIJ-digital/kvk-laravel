<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests\Data;

use DIJ\Kvk\Data\Responses\BaseProfileResponse;
use DIJ\Kvk\Data\ValueObjects\MaterialRegistration;
use DIJ\Kvk\Data\ValueObjects\SbiActivity;
use DIJ\Kvk\Data\ValueObjects\TradeName;
use DIJ\Kvk\Exceptions\KvkException;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Http\Client\Response;
use PHPUnit\Framework\TestCase;

final class BaseProfileResponseTest extends TestCase
{
    public function test_from_array_maps_required_fields(): void
    {
        $data = [
            'kvkNummer' => '69599068',
            'indNonMailing' => 'Ja',
            'naam' => 'Test Stichting Bolderbast',
        ];
        $result = BaseProfileResponse::fromArray($data);
        self::assertSame('69599068', $result->kvkNumber);
        self::assertSame('Ja', $result->nonMailingIndicator);
        self::assertSame('Test Stichting Bolderbast', $result->name);
        self::assertNull($result->formalRegistrationDate);
        self::assertNull($result->materialRegistration);
        self::assertNull($result->statutoryName);
        self::assertSame([], $result->tradeNames);
        self::assertSame([], $result->sbiActivities);
        self::assertSame([], $result->links);
    }

    public function test_from_array_maps_all_fields(): void
    {
        $data = [
            'kvkNummer' => '69599068',
            'indNonMailing' => 'Ja',
            'naam' => 'Test Stichting Bolderbast',
            'formeleRegistratiedatum' => '20150622',
            'materieleRegistratie' => ['datumAanvang' => '20150101', 'datumEinde' => null],
            'statutaireNaam' => 'Stichting Bolderbast',
            'handelsnamen' => [['naam' => 'Test Stichting Bolderbast', 'volgorde' => 1]],
            'sbiActiviteiten' => [['sbiCode' => '86101', 'sbiOmschrijving' => 'Universitair medisch centra', 'indHoofdactiviteit' => 'Ja']],
            'links' => [['rel' => 'self', 'href' => 'https://api.kvk.nl/api/v1/basisprofielen/69599068']],
        ];
        $result = BaseProfileResponse::fromArray($data);
        self::assertSame('20150622', $result->formalRegistrationDate);
        self::assertInstanceOf(MaterialRegistration::class, $result->materialRegistration);
        self::assertSame('Stichting Bolderbast', $result->statutoryName);
        self::assertCount(1, $result->tradeNames);
        self::assertInstanceOf(TradeName::class, $result->tradeNames[0]);
        self::assertCount(1, $result->sbiActivities);
        self::assertInstanceOf(SbiActivity::class, $result->sbiActivities[0]);
        self::assertCount(1, $result->links);
    }

    public function test_from_response_parses_valid_json(): void
    {
        $response = new Response(
            new Psr7Response(200, ['Content-Type' => 'application/json'], json_encode([
                'kvkNummer' => '69599068',
                'indNonMailing' => 'Nee',
                'naam' => 'Test BV',
            ], JSON_THROW_ON_ERROR)),
        );
        $result = BaseProfileResponse::fromResponse($response);
        self::assertSame('69599068', $result->kvkNumber);
    }

    public function test_from_response_throws_on_invalid_body(): void
    {
        $response = new Response(
            new Psr7Response(200, ['Content-Type' => 'application/json'], json_encode('not-an-array', JSON_THROW_ON_ERROR)),
        );
        $this->expectException(KvkException::class);
        BaseProfileResponse::fromResponse($response);
    }

    public function test_fake_returns_correct_defaults(): void
    {
        $result = BaseProfileResponse::fake();
        self::assertSame('69599068', $result->kvkNumber);
        self::assertSame('Test Stichting Bolderbast', $result->name);
        self::assertSame('Stichting Bolderbast', $result->statutoryName);
        self::assertInstanceOf(MaterialRegistration::class, $result->materialRegistration);
        self::assertCount(1, $result->tradeNames);
        self::assertCount(1, $result->sbiActivities);
    }
}
