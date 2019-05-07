<?php

namespace Leeovery\LaravelSettings;

use Illuminate\Support\Facades\Facade;

class LaravelSettingsFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-settings';
    }
}
