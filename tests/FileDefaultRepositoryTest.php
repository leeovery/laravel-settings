<?php

namespace Leeovery\LaravelSettings\Tests;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Leeovery\LaravelSettings\Defaults\FileDefaultRepository;

class FileDefaultRepositoryTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Config::set('settings-test', [
            'global' => [
                'email' => true,
            ],
        ]);
    }

    /**
     * @test
     */
    public function returns_defaults_when_given_key()
    {
        $defaults = (new FileDefaultRepository)->get('test');

        $this->assertInstanceOf(Collection::class, $defaults);
        $this->assertEquals($defaults->all(), [
            'global' => [
                'email' => true,
            ],
        ]);
    }
}