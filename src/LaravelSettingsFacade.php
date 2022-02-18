<?php

namespace Leeovery\LaravelSettings;

use Illuminate\Support\Facades\Facade;

class LaravelSettingsFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-settings';
    }
}
