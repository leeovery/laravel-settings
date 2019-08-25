<?php

namespace Leeovery\LaravelSettings;

use Illuminate\Database\Eloquent\Model;

class SettingsConfig
{
    /**
     * @var Model
     */
    public $model;

    /**
     * SettingsConfig constructor.
     *
     * @param  Model  $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }
}