<?php

namespace Leeovery\LaravelSettings\Event;

use Illuminate\Foundation\Events\Dispatchable;

class SettingUpdated
{
    use Dispatchable;

    public $fullMergedSettings;

    public $userId;

    public $storedSettings;

    public $settingsBeingSet;

    public function __construct($settingsBeingSet, $fullMergedSettings, $storedSettings, $userId)
    {
        $this->settingsBeingSet = $settingsBeingSet;
        $this->fullMergedSettings = $fullMergedSettings;
        $this->storedSettings = $storedSettings;
        $this->userId = $userId;
    }
}
