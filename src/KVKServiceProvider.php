<?php

declare(strict_types=1);

namespace DIJ\Kvk;

use DIJ\Kvk\Data\Settings;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\ServiceProvider;
use Override;

class KVKServiceProvider extends ServiceProvider
{
    #[Override]
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/kvk.php', 'kvk');

        $this->app->singleton(Settings::class, function () {
            /** @var Repository $configRepo */
            $configRepo = $this->app->make('config');

            /** @var array<string, string> $config */
            $config = $configRepo->get('kvk');

            return Settings::fromArray($config);
        });
        $this->app->singleton(KVKGateway::class);
        $this->app->singleton(KVK::class);
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/kvk.php' => $this->app->configPath('kvk.php'),
        ], 'kvk');
    }
}
