<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests\Data;

use DIJ\Kvk\Data\ValueObjects\SbiActivity;
use PHPUnit\Framework\TestCase;

final class SbiActivityTest extends TestCase
{
    public function test_from_array_returns_instance_with_correct_properties(): void
    {
        $activity = SbiActivity::fromArray([
            'sbiCode' => '86101',
            'sbiOmschrijving' => 'Universitair medisch centra',
            'indHoofdactiviteit' => 'Ja',
        ]);

        self::assertSame('86101', $activity->sbiCode);
        self::assertSame('Universitair medisch centra', $activity->sbiDescription);
        self::assertSame('Ja', $activity->mainActivityIndicator);
    }

    public function test_from_array_with_different_values(): void
    {
        $activity = SbiActivity::fromArray([
            'sbiCode' => '47110',
            'sbiOmschrijving' => 'Winkels in supermarkten',
            'indHoofdactiviteit' => 'Nee',
        ]);

        self::assertSame('47110', $activity->sbiCode);
        self::assertSame('Winkels in supermarkten', $activity->sbiDescription);
        self::assertSame('Nee', $activity->mainActivityIndicator);
    }

    public function test_fake_returns_instance_with_correct_defaults(): void
    {
        $activity = SbiActivity::fake();

        self::assertSame('86101', $activity->sbiCode);
        self::assertSame('Universitair medisch centra', $activity->sbiDescription);
        self::assertSame('Ja', $activity->mainActivityIndicator);
    }
}
