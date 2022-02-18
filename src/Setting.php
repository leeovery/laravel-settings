<?php

namespace Leeovery\LaravelSettings;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $guarded = [];

    protected $casts = [
        'settings' => 'json',
    ];
}
