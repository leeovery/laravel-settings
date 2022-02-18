<?php

namespace Leeovery\LaravelSettings\Defaults;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Leeovery\LaravelSettings\Exceptions\InvalidSettingsKey;
use function DeepCopy\deep_copy;

class FileDefaultRepository implements DefaultRepository
{
    public function get(string $key): Collection
    {
        $defaults = collect(
            $this->ensureSubSetsAreProperlyKeyed(
                $key, config($this->makeKey($key))
            )
        );

        throw_if($defaults->isEmpty(), InvalidSettingsKey::class);

        return deep_copy($defaults);
    }

    private function ensureSubSetsAreProperlyKeyed(string $key, $defaults)
    {
        // Ensure results are fully keyed when only fetching a subset of data.
        if (Str::contains($key, '.')) {
            $key = Str::after($key, '.');
            foreach (Arr::dot($defaults) as $dottedKey => $value) {
                Arr::set($defaults, $key.'.'.$dottedKey, $value);
                Arr::forget($defaults, $dottedKey);
            }
            $defaults = array_filter($defaults);
        }

        return $defaults;
    }

    private function makeKey($key): string
    {
        return config('laravel-settings.config-pre-key').'-'.$key;
    }
}
