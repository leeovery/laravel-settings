<?php

namespace Leeovery\LaravelSettings\Defaults;

use Illuminate\Support\Collection;
use Leeovery\LaravelSettings\Exceptions\InvalidSettingsKey;

class FileDefaultRepository implements DefaultRepository
{
    public function get(string $key): Collection
    {
        $defaults = collect(config($this->makeKey($key)));

        if ($defaults->isEmpty()) {
            throw new InvalidSettingsKey;
        }

        return $defaults;
    }

    private function makeKey($key)
    {
        return config('laravel-settings.config-pre-key').'-'.$key;
    }
}