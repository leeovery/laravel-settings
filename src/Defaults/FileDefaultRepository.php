<?php

namespace Leeovery\LaravelSettings\Defaults;

class FileDefaultRepository implements DefaultRepository
{
    public function get(string $key)
    {
        return config(config('laravel-settings.config-pre-key').'-'.$key);
    }
}