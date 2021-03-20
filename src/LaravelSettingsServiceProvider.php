<?php

namespace Leeovery\LaravelSettings;

use Illuminate\Contracts\Container\Container;
use Leeovery\LaravelSettings\Defaults\DefaultRepository;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelSettingsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-settings')
            ->hasMigration('create_settings_table')
            ->hasConfigFile('laravel-settings');
    }

    public function packageRegistered()
    {
        $config = config('laravel-settings');

        $this->app->bind(DefaultRepository::class, $config['defaults']['provider']);

        $this->app->bind('laravel-settings', function (Container $app) use ($config) {
            return new LaravelSettings(
                $app->make(DefaultRepository::class),
                new SettingsConfig(new $config['settings-model']),
            );
        });
    }
}
