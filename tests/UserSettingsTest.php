<?php

namespace Leeovery\LaravelSettings\Tests;

use PHPUnit\Framework\TestCase;

class UserSettingsTest extends TestCase
{
    /**
     * @test
     */
    public function has_default_values_for_every_user()
    {
        // SettingManager is overall object which controls all below.
        // Setting is a model used for fetching user settings from db
        // DefaultSettings = contract for fetching defaults
        // FileDefaultSettings = concrete implementation of DefaultSettings for file (gets defaults from config/settings.php file)
        // DatabaseDefaultSettings = concrete implen. of DefaultSettings for DB (get defaults from database)
        // SettingsCache = contract for caching layer
        // LaravelSettingsCache = concrete of SettingsCache (controls storing and fetching settings from defined cache in app)



        // SettingManager class - aliases by settings() global function (and facade?)
        // Ask SettingManager for defaults:
        // SettingManager was given DefaultSettings contract (DefaultSettingsFile || DefaultSettingsDatabase concrete) in service provider
        // SettingManager asks DefaultSettings for defaults
        // DefaultSettings uses concrete for defaults
        // initially these will be from config file (class DefaultSettingsFile implements DefaultSettings)
        // later we can write a new DefaultSettings manager using DB (DefaultSettingsDatabase)
        // either way SettingManager class will get defaults as an assoc. array.
        // SettingManager class then needs to fetch user settings from storage (using Setting model)
        // and merge user settings with defaults
        // Those combined settings are then stored as a collection


    }
}
