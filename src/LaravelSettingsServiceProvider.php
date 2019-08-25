<?php

namespace Leeovery\LaravelSettings;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Container\Container;
use Leeovery\LaravelSettings\Defaults\DefaultRepository;

class LaravelSettingsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('laravel-settings.php'),
            ], 'config');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'laravel-settings');

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
