<?php

namespace Leeovery\LaravelSettings;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
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

    private $baseKey;

    private $userId;

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

    public function baseKey($baseKey)
    {
        $this->baseKey = $baseKey;

        return $this;
    }

    public function forUser($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    public function set(array $newSettings)
    {
        /** @var Collection $settings */
        $settings = $this->get()->all();

        foreach ($newSettings as $key => $value) {
            if (Arr::has($settings, $key)) {
                data_set($settings, $key, $value);
            }
        }

        /** @var Collection $defaults */
        $defaults = $this->defaultRepository->get($this->baseKey);

        $forStoring = $this->arrayRecursiveDiff($settings, $defaults->all());

        // get ALL stored settings for user
        $allStoredSettings = optional($this->getStoredSettings())->settings;

        // if we have nothing to store and stored settings is not null...
        if (empty($forStoring) && $allStoredSettings) {
            unset($allStoredSettings[$this->baseKey]);
        } else {
            $allStoredSettings[$this->baseKey] = $forStoring;
        }

        // if $allStoredSettings is empty then we can delete settings for user
        // else save
        if (empty($allStoredSettings)) {
            $this->deleteSettingsForUser();
        } else {
            $this->storeSettings($allStoredSettings);
        }
    }

    public function get($key = null): Collection
    {
        /** @var Collection $settings */
        $settings = $this->defaultRepository->get($this->makeKey($key));

        if (! is_null($this->userId) && $this->entityHasStoredSettings()) {

            // get ALL stored settings for user
            $storedSettings = collect($this->getStoredSettings()->settings[$this->baseKey]);

            // does user have stored settings for this baseKey??
            if ($storedSettings->isEmpty()) {
                return $settings;
            }

            // does stored settings have the key we are wanting?
            if (is_null($key) || $storedSettings->has($key)) {
                $settings = collect(array_replace_recursive($settings->all(), $storedSettings->all()));
            }

        }

        return $settings;
    }

    private function makeKey($key = null)
    {
        return $this->baseKey.(! is_null($key) ? '.'.$key : '');
    }

    /**
     * @return bool
     */
    private function entityHasStoredSettings(): bool
    {
        return Setting::where('user_id', $this->userId)->count() > 0;
    }

    /**
     * @return mixed
     */
    private function getStoredSettings()
    {
        return Setting::where('user_id', $this->userId)->first();
    }

    private function arrayRecursiveDiff($aArray1, $aArray2)
    {
        $aReturn = [];

        foreach ($aArray1 as $mKey => $mValue) {
            if (array_key_exists($mKey, $aArray2)) {
                if (is_array($mValue)) {
                    $aRecursiveDiff = $this->arrayRecursiveDiff($mValue, $aArray2[$mKey]);
                    if (count($aRecursiveDiff)) {
                        $aReturn[$mKey] = $aRecursiveDiff;
                    }
                } else {
                    if ($mValue != $aArray2[$mKey]) {
                        $aReturn[$mKey] = $mValue;
                    }
                }
            } else {
                $aReturn[$mKey] = $mValue;
            }
        }

        return $aReturn;
    }

    private function deleteSettingsForUser()
    {
        return Setting::where('user_id', $this->userId)->delete();
    }

    private function storeSettings($settings)
    {
        return Setting::updateOrCreate(['user_id' => $this->userId], [
            'user_id'  => $this->userId,
            'settings' => $settings,
        ]);
    }
}
