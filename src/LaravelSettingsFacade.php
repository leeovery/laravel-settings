<?php

namespace Leeovery\LaravelSettings;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Leeovery\LaravelSettings\Skeleton\SkeletonClass
 */
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
