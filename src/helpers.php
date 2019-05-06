<?php

if (! function_exists('settings')) {
    function settings($key = null, $userId = null)
    {
        $setting = app('laravel-settings');

        if (is_null($key)) {
            return $setting;
        }

        if (is_array($key)) {
            $setting->set($key, $userId);

            return $setting;
        }

        return $setting->get($key, $userId);
    }
}