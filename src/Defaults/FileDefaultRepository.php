<?php

namespace Leeovery\LaravelSettings\Defaults;

use Illuminate\Support\Collection;

class FileDefaultRepository implements DefaultRepository
{
    public function get(string $key): Collection
    {
        return collect(config($this->makeKey($key)));
    }

    private function makeKey($key)
    {
        return config('laravel-settings.config-pre-key').'-'.$key;
    }
}