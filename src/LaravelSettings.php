<?php

namespace Leeovery\LaravelSettings;

use Illuminate\Support\Arr;
use Leeovery\LaravelSettings\Cache\CacheRepository;
use Leeovery\LaravelSettings\Defaults\DefaultRepository;

class LaravelSettings
{
    /**
     * @var DefaultRepository
     */
    private $defaultRepository;

    /**
     * @var CacheRepository
     */
    private $cacheRepository;

    /**
     * LaravelSettings constructor.
     *
     * @param  DefaultRepository  $defaultRepository
     * @param  CacheRepository  $cacheRepository
     */
    public function __construct(DefaultRepository $defaultRepository, CacheRepository $cacheRepository)
    {
        $this->defaultRepository = $defaultRepository;
        $this->cacheRepository = $cacheRepository;
    }

    public function set(array $values, $userId = null)
    {
        $baseKey = array_key_first($values);
        $newSettings = $values[$baseKey];

        // get current user settings
        $settings = $this->get($baseKey, $userId);

        // we need to check this option exists in the default settings
        // if it doesnt then ignore
        // break it down into sections?

        foreach ($newSettings as $key => $value) {
            if (Arr::has($settings, $key)) {
                data_set($settings, $key, $value);
            }
        }

        dd($settings);
    }

    public function get($key, $userId = null)
    {
        $settings = $this->defaultRepository->get($key);

        if (! is_null($userId) && $this->entityHasStoredSettings($userId)) {

            $storedSettings = Setting::where('user_id', $userId)->first();

            $settings = array_replace_recursive($settings, $storedSettings->settings);

        }

        return $settings;
    }

    /**
     * @param $userId
     * @return bool
     */
    private function entityHasStoredSettings($userId): bool
    {
        return Setting::where('user_id', $userId)->count() > 0;
    }
}
