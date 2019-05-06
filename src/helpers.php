<?php

if (! function_exists('settings')) {
    function settings($baseKey = null, $userId = null)
    {
        $setting = app('laravel-settings');

        if (is_null($baseKey)) {
            return $setting;
        }

        return $setting->baseKey($baseKey)
                       ->forUser($userId);
    }
}