<?php

namespace Leeovery\LaravelSettings\Defaults;

interface DefaultRepository
{
    public function get(string $key);
}