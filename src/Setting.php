<?php

namespace Leeovery\LaravelSettings;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $casts = [
        'settings' => 'json',
    ];
}