<?php

namespace Leeovery\LaravelSettings\Tests;

use CreateSettingsTable;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Schema;
use Leeovery\LaravelSettings\LaravelSettingsServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase($this->app);
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelSettingsServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function setUpDatabase($app)
    {
        Schema::dropAllTables();

        include_once __DIR__.'/../database/migrations/create_settings_table.php.stub';

        (new CreateSettingsTable())->up();
    }
}
