<?php

declare(strict_types=1);

namespace DIJ\Kvk\Tests;

use DIJ\Kvk\Data\Settings;
use DIJ\Kvk\KVK;
use DIJ\Kvk\KVKGateway;
use DIJ\Kvk\KVKServiceProvider;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\CachesConfiguration;
use PHPUnit\Framework\TestCase;

final class KVKServiceProviderTest extends TestCase
{
    public function test_register_binds_settings_gateway_and_kvk_singletons(): void
    {
        $app = new FakeTestApp;

        $configRepository = $this->createStub(Repository::class);
        $configRepository->method('get')->willReturn([
            'base_url' => 'https://api.kvk.nl/test',
            'api_key' => 'test-key',
        ]);

        $app->instance('config', $configRepository);

        $provider = new KVKServiceProvider($app);
        $provider->register();

        $settings = $app->make(Settings::class);
        $gateway = $app->make(KVKGateway::class);
        $kvk = $app->make(KVK::class);

        self::assertInstanceOf(Settings::class, $settings);
        self::assertSame('https://api.kvk.nl/test', $settings->base_url);
        self::assertSame('test-key', $settings->api_key);
        self::assertInstanceOf(KVKGateway::class, $gateway);
        self::assertInstanceOf(KVK::class, $kvk);

        self::assertSame($settings, $app->make(Settings::class));
        self::assertSame($gateway, $app->make(KVKGateway::class));
        self::assertSame($kvk, $app->make(KVK::class));
    }

    public function test_boot_registers_kvk_publish_path(): void
    {
        $app = new FakeTestApp;

        $provider = new KVKServiceProvider($app);

        $provider->boot();

        $paths = KVKServiceProvider::pathsToPublish(KVKServiceProvider::class, 'kvk');
        $configSource = dirname(__DIR__).'/src/../config/kvk.php';

        self::assertIsArray($paths);
        self::assertArrayHasKey($configSource, $paths);
        self::assertSame('/fake-config/kvk.php', $paths[$configSource]);
    }
}

final class FakeTestApp implements CachesConfiguration
{
    public array $bindings = [];

    public array $instances = [];

    public function singleton(string $abstract, mixed $concrete = null): void
    {
        $this->bindings[$abstract] = $concrete ?? $abstract;
    }

    public function instance(string $abstract, mixed $instance): void
    {
        $this->instances[$abstract] = $instance;
    }

    public function make(string $abstract): mixed
    {
        if (array_key_exists($abstract, $this->instances)) {
            return $this->instances[$abstract];
        }

        $concrete = $this->bindings[$abstract] ?? $abstract;

        if ($concrete instanceof \Closure) {
            $instance = $concrete();
        } elseif ($concrete === KVKGateway::class) {
            $instance = new KVKGateway(new \Illuminate\Http\Client\Factory, $this->make(Settings::class));
        } elseif ($concrete === KVK::class) {
            $instance = new KVK($this->make(KVKGateway::class));
        } elseif (is_string($concrete)) {
            $instance = new $concrete;
        } else {
            $instance = $concrete;
        }

        $this->instances[$abstract] = $instance;

        return $instance;
    }

    public function configPath(string $path = ''): string
    {
        return '/fake-config/'.$path;
    }

    public function configurationIsCached(): bool
    {
        return true;
    }

    public function getCachedConfigPath(): string
    {
        return '/fake-config/cache.php';
    }

    public function getCachedServicesPath(): string
    {
        return '/fake-config/services.php';
    }
}
