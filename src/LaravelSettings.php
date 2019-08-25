<?php

namespace Leeovery\LaravelSettings;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Leeovery\LaravelSettings\Defaults\DefaultRepository;

class LaravelSettings
{
    /**
     * @var DefaultRepository
     */
    private $defaultRepository;

    private $baseKey;

    private $userId;

    /**
     * @var Model
     */
    private $model;

    /**
     * @var SettingsConfig
     */
    private $settingsConfig;

    /**
     * LaravelSettings constructor.
     *
     * @param  DefaultRepository  $defaultRepository
     * @param  SettingsConfig     $settingsConfig
     */
    public function __construct(DefaultRepository $defaultRepository, SettingsConfig $settingsConfig)
    {
        $this->defaultRepository = $defaultRepository;
        $this->settingsConfig = $settingsConfig;
        $this->model = $this->settingsConfig->model;
    }

    public static function setting($value, $label = null, $validator = null)
    {
        return SettingStore::make($value, $label, $validator);
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
        // Fetch defaults and merge in any custom settings for user
        $settings = $this->get()->all();

        // If current settings we just fetched have the keys from the
        // new settings we're trying to set, then go ahead and set.
        foreach ($newSettings as $key => $value) {
            if (Arr::has($settings, $key)) {
                // If value here is an instance of SettingsStore then use method
                // on object. Otherwise simply set value directly to array...
                if (is_a($currentValue = Arr::get($settings, $key), SettingStore::class, true)) {
                    $currentValue->set($value);
                } else {
                    data_set($settings, $key, $value);
                }
            }
        }

        // Now fetch defaults with no custom changes...
        /** @var Collection $defaults */
        $defaults = $this->defaultRepository->get($this->baseKey);

        // Now remove all defaults from the new settings array we
        // created above. Whatever's left over needs persisting.
        $forStoring = $this->arrayRecursiveDiff($settings, $defaults->all());

        // Get ALL stored settings for user.
        $allStoredSettings = optional($this->getStoredSettings())->settings;

        // if we have nothing to store and stored settings is not null...
        if (empty($forStoring) && $allStoredSettings[$this->baseKey]) {
            unset($allStoredSettings[$this->baseKey]);
        } else {
            if (!empty($forStoring)) {
                $allStoredSettings[$this->baseKey] = $forStoring;
            }
        }

        // if $allStoredSettings is empty then we can delete settings for user
        // else save...
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

        if (!is_null($this->userId) && $this->entityHasStoredSettingsForBaseKey()) {

            // get ALL stored settings for user
            $storedSettings = $this->getStoredSettings()->settings[$this->baseKey];

            // does stored settings have the key we are wanting?
            if (is_null($key) || Arr::has($storedSettings, $key)) {
                $settings = $this->arrayRecursiveReplace($settings->all(), $storedSettings);
            }
        }

        return $settings;
    }

    private function makeKey($key = null)
    {
        return $this->baseKey.(!is_null($key) ? '.'.$key : '');
    }

    /**
     * @return bool
     */
    private function entityHasStoredSettingsForBaseKey(): bool
    {
        return $this->model::where('user_id', $this->userId)
                           ->where('settings', 'LIKE', "%{$this->baseKey}%")
                           ->count() > 0;
    }

    /**
     * @return mixed
     */
    private function getStoredSettings()
    {
        return $this->model::where('user_id', $this->userId)->first();
    }

    public function arrayRecursiveReplace($settings, $fromStorage): Collection
    {
        foreach (Arr::dot($fromStorage) as $key => $value) {
            if (Arr::has($settings, $key)) {
                if (is_a($fromDefault = Arr::get($settings, $key), SettingStore::class, true)) {
                    Arr::set($settings, $key, $fromDefault->set($value));
                } else {
                    Arr::set($settings, $key, $value);
                }
            }
        }

        return collect($settings);
    }

    private function arrayRecursiveDiff($newSettings, $defaultSettings)
    {
        $storeTheseSettings = [];
        foreach ($newSettings as $newSettingKey => $newSettingValue) {
            if (array_key_exists($newSettingKey, $defaultSettings)) {
                if (is_array($newSettingValue)) {
                    $aRecursiveDiff = $this->arrayRecursiveDiff($newSettingValue, $defaultSettings[$newSettingKey]);
                    if (count($aRecursiveDiff)) {
                        $storeTheseSettings[$newSettingKey] = $aRecursiveDiff;
                    }
                } else {
                    if (is_a($newSettingValue, SettingStore::class, true)) {
                        if (!$newSettingValue->compareValues($defaultSettings[$newSettingKey])) {
                            /** @var SettingStore $newSettingValue */
                            $storeTheseSettings[$newSettingKey] = $newSettingValue->getValue();
                        }
                    } else {
                        if ($newSettingValue !== $defaultSettings[$newSettingKey]) {
                            $storeTheseSettings[$newSettingKey] = $newSettingValue;
                        }
                    }
                }
            } else {
                $storeTheseSettings[$newSettingKey] = $newSettingValue;
            }
        }

        return $storeTheseSettings;
    }

    private function deleteSettingsForUser()
    {
        return $this->model::where('user_id', $this->userId)->delete();
    }

    private function storeSettings($settings)
    {
        return $this->model::updateOrCreate(['user_id' => $this->userId], [
            'user_id'  => $this->userId,
            'settings' => $settings,
        ]);
    }
}
