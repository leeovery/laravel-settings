<?php

namespace Leeovery\LaravelSettings;

use Illuminate\Database\Eloquent\Model;

class SettingsConfig
{
    public Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }
}
