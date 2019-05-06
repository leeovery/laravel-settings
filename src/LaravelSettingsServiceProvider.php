<?php

namespace Leeovery\LaravelSettings;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use Leeovery\LaravelSettings\Cache\CacheRepository;
use Leeovery\LaravelSettings\Defaults\DefaultRepository;

class LaravelSettingsServiceProvider extends ServiceProvider
{
    // LaravelSettings is overall object which controls all below.
    // Setting is a model used for fetching user settings from db
    // DefaultSettings = contract for fetching defaults
    // FileDefaultSettings = concrete implementation of DefaultSettings for file (gets defaults from config/settings.php file)
    // DatabaseDefaultSettings = concrete implementation of DefaultSettings for DB (get defaults from database)
    // SettingsCache = contract for caching layer
    // LaravelSettingsCache = concrete of SettingsCache (controls storing and fetching settings from defined cache in app)

    // LaravelSettings class - aliases by settings() global function (and facade?)
    // Ask LaravelSettings for defaults:
    // LaravelSettings was given DefaultSettings contract (DefaultSettingsFile || DefaultSettingsDatabase concrete) in service provider
    // LaravelSettings asks DefaultSettings for defaults
    // DefaultSettings uses concrete for defaults
    // initially these will be from config file (class DefaultSettingsFile implements DefaultSettings)
    // later we can write a new DefaultSettings manager using DB (DefaultSettingsDatabase)
    // either way LaravelSettings class will get defaults as an assoc. array.
    // LaravelSettings class then needs to fetch user settings from storage (using Setting model)
    // and merge user settings with defaults
    // Those combined settings are then stored as a collection

    // get defaults

    // use settings() to get/set settings
    // use case:
    // need settings for user where ID === 1
    // can ask settings() for what we need and give a user id
    // eg. settings('user-notifications', 1)
    // 1st param is lookup key for settings we need
    // 2nd param is constraint id (using default constraint type === 'user_id')
    // this will get defaults using the set default repo.
    // and merge them with the stored settings we get from storage
    // and we get those settings from storage using Setting model.

    // store settings in config file named settings-{name}.php
    // where {name} will be used as the initial key in the
    // config array. eg:
    // settings-user-notifications.php
    // 'user-notifications' => [...]

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

            // Registering package commands.
            // $this->commands([]);
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
        $this->app->bind(CacheRepository::class, $config['cache']['provider']);

        $this->app->bind('laravel-settings', function (Container $app) {
            return new LaravelSettings(
                $app->make(DefaultRepository::class),
                $app->make(CacheRepository::class)
            );
        });
    }
}
