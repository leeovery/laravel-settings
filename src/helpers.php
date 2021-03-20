<?php

use Leeovery\LaravelSettings\LaravelSettings;

if (! function_exists('settings')) {
    /**
     * @param  null  $baseKey
     * @param  null  $userId
     * @return LaravelSettings
     */
    function settings($baseKey = null, $userId = null): LaravelSettings
    {
        $setting = app('laravel-settings');

        if (is_null($baseKey)) {
            return $setting;
        }

        return $setting
            ->baseKey($baseKey)
            ->forUser($userId);
    }
}
