<?php

return [

    /**
     * This configuration option is used to tell this package what config files
     * it should care about. Best to use an example to make this clear, but
     * generally speaking you won't need to change it. But if you need to
     * then you need to be aware of what impact it has.
     *
     * By default it's set to 'settings', which means the config files this
     * package will care about should be named using that pre-key. So if
     * you are making a settings file for user-notifications, then it
     * should be named:
     *
     * /config/settings-user-notifications.php
     *
     */
    'config-pre-key' => 'settings',

    /**
     * This is the model used to persist the settings in the DB. Usually you wouldn't
     * need to overwrite this but if you have a use-case you can do so right here.
     * If you override it be sure to set the table name to settings, or provide
     * your own migration.
     */
    'settings-model' => \Leeovery\LaravelSettings\Setting::class,

    /**
     * This is the repository for fetching and stored settings.
     */
    'defaults'       => [
        'provider' => \Leeovery\LaravelSettings\Defaults\FileDefaultRepository::class,
    ],

    ///**
    // * Caching hasn't been implemented yet.... WIP
    // */
    //'cache' => [
    //    'enabled'  => true,
    //    'provider' => \Leeovery\LaravelSettings\Cache\LaravelCacheRepository::class,
    //],

];
