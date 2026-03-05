<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests\Data;

use DIJ\Kvk\Data\ValueObjects\MaterialRegistration;
use PHPUnit\Framework\TestCase;

final class MaterialRegistrationTest extends TestCase
{
    public function test_from_array_returns_instance_with_correct_properties(): void
    {
        $registration = MaterialRegistration::fromArray([
            'datumAanvang' => '20150101',
            'datumEinde' => '20201231',
        ]);

        self::assertSame('20150101', $registration->startDate);
        self::assertSame('20201231', $registration->endDate);
    }

    public function test_from_array_handles_optional_fields(): void
    {
        $registration = MaterialRegistration::fromArray([]);

        self::assertNull($registration->startDate);
        self::assertNull($registration->endDate);
    }

    public function test_from_array_handles_null_end_date(): void
    {
        $registration = MaterialRegistration::fromArray([
            'datumAanvang' => '20150101',
            'datumEinde' => null,
        ]);

        self::assertSame('20150101', $registration->startDate);
        self::assertNull($registration->endDate);
    }

    public function test_fake_returns_instance_with_correct_defaults(): void
    {
        $registration = MaterialRegistration::fake();

        self::assertSame('20150101', $registration->startDate);
        self::assertNull($registration->endDate);
    }

    public function test_to_array_maps_to_dutch_keys(): void
    {
        $registration = MaterialRegistration::fake();

        $array = $registration->toArray();

        self::assertSame('20150101', $array['datumAanvang']);
        self::assertNull($array['datumEinde']);
    }
}
