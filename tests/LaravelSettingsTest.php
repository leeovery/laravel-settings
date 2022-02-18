<?php

namespace Leeovery\LaravelSettings\Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Leeovery\LaravelSettings\Event\SettingUpdated;

class LaravelSettingsTest extends TestCase
{
    /**
     * @test
     */
    public function can_override_setting_model()
    {
        settings('notifications-test', 1)
            ->set([
                'global.email'    => '111',
                'global.sms'      => '222',
                'global.database' => '333',
            ]);

        $settings = settings('notifications-test', 1)->get();

        $this->assertEquals(1, CustomSettingModel::where('user_id', 1)->count());
        $this->assertEquals('111', (string) Arr::get($settings, 'global.email'));
    }

    /**
     * @test
     */
    public function will_dispatch_event_when_settings_changed()
    {
        Event::fake([SettingUpdated::class]);
        settings('notifications-test', 1)->set(['global.email' => '111']);
        Event::assertDispatched(SettingUpdated::class);
    }

    /**
     * @test
     */
    public function will_dispatch_event_when_settings_changed_back_to_default()
    {
        settings('notifications-test', 1)->set(['global.email' => '111']);
        Event::fake([SettingUpdated::class]);
        settings('notifications-test', 1)->set(['global.email' => '000']);
        Event::assertDispatched(SettingUpdated::class);
    }

    public function setUp(): void
    {
        parent::setUp();

        Config::set('laravel-settings.settings-model', CustomSettingModel::class);

        Config::set('settings-notifications-test', [
            'global' => [
                'email'    => '000',
                'sms'      => '000',
                'database' => '000',
            ],
        ]);
    }
}

class CustomSettingModel extends Model
{
    protected $table = 'settings';

    protected $guarded = [];

    protected $casts = [
        'settings' => 'json',
    ];
}
