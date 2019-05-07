<?php

return [

    /**
     * This is what's expected when creating custom config files.
     * eg. If you want default settings for user notifications
     * you'd create a config file as follows:
     *
     * /config/{config-pre-key}-user-notifications.php
     *
     * ...by default...
     * /config/settings-user-notifications.php
     */
    'config-pre-key' => 'settings',

    'defaults' => [
        'provider' => \Leeovery\LaravelSettings\Defaults\FileDefaultRepository::class,
    ],

    'cache' => [
        'enabled'  => true,
        'provider' => \Leeovery\LaravelSettings\Cache\LaravelCacheRepository::class,
    ],

];