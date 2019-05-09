<?php

namespace Leeovery\LaravelSettings\Tests;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Leeovery\LaravelSettings\Exceptions\InvalidSettingsKey;
use Leeovery\LaravelSettings\LaravelSettings;
use Leeovery\LaravelSettings\Setting;
use Leeovery\LaravelSettings\SettingStore;

class ValuesAsSettingStoreObjectTest extends TestCase
{
    public $notificationSettings;

    public $privacySettings;

    public function setUp(): void
    {
        parent::setUp();

        Config::set('settings-notifications-test', $this->notificationSettings = [
            'global' => [
                'email'    => LaravelSettings::setting(true, 'global email label'),
                'sms'      => SettingStore::make(true, 'global sms label'),
                'database' => LaravelSettings::setting(true, 'global database label'),
            ],
            'orders' => [
                'new' => [
                    'email'    => SettingStore::make(true, 'orders new email label'),
                    'sms'      => LaravelSettings::setting(true, 'orders new sms label'),
                    'database' => LaravelSettings::setting(true, 'orders new database label'),
                ],
            ],
        ]);

        Config::set('settings-privacy-test', $this->privacySettings = [
            'section1' => [
                '111' => SettingStore::make(true, 'section1 111 label'),
                '222' => LaravelSettings::setting(true, 'section1 222 label'),
                '333' => LaravelSettings::setting(true, 'section1 333 label'),
            ],
            'section2' => [
                'aaa' => LaravelSettings::setting(true, 'section2 aaa label'),
                'bbb' => LaravelSettings::setting(true, 'section2 bbb label'),
                'ccc' => SettingStore::make(true, 'section2 ccc label'),
            ],
        ]);
    }

    /**
     * @test
     */
    public function can_get_settings_using_settings_store_object_in_config()
    {
        $settings = settings('notifications-test', 1)->get();

        $this->assertEquals($settings->all(), $this->notificationSettings);
    }

    /**
     * @test
     */
    public function can_set_settings_for_user_from_object_config()
    {
        settings('notifications-test', 1)
            ->set([
                'global.email'     => '123',
                'orders.new.email' => '456',
            ]);

        $settings = settings('notifications-test', 1)->get();

        $this->assertEquals(Setting::where('user_id', 1)->count(), 1);
        $this->assertEquals('123', (string) Arr::get($settings, 'global.email'));
        $this->assertEquals('456', (string) Arr::get($settings, 'orders.new.email'));
        $this->assertEquals(true, (bool) Arr::get($settings, 'orders.new.sms'));
    }

    /**
     * @test
     */
    public function will_delete_custom_settings_for_user_when_they_have_changed_the_last_one__for_object()
    {
        settings('notifications-test', 1)
            ->set([
                'global.email' => 'vvv',
            ]);

        $settings = settings('notifications-test')->forUser(1)
                                                  ->get();

        $this->assertEquals(Setting::where('user_id', 1)->count(), 1);
        $this->assertEquals('vvv', (string) Arr::get($settings, 'global.email'));

        // set again to match default...
        settings('notifications-test', 1)
            ->set([
                'global.email' => true,
            ]);

        $settings = settings('notifications-test', 1)->get();

        $this->assertEquals(true, (bool) Arr::get($settings, 'global.email'));
        $this->assertEquals(Setting::where('user_id', 1)->count(), 0);
    }

    /**
     * @test
     */
    public function will_ignore_passed_new_settings_if_they_do_not_exist_in_default__for_object()
    {
        settings('notifications-test', 1)
            ->set([
                'i.do.not.exist' => 'xxx1', // doesnt exist in defaults so wont be persisted.
                'global.email'   => 'xxx2', // this does exist in defaults
            ]);

        $settings = settings('notifications-test')->forUser(1)
                                                  ->get();

        $this->assertEquals(Setting::where('user_id', 1)->count(), 1);
        $this->assertEquals(null, Arr::get($settings, 'i.do.not.exist'));
        $this->assertEquals('xxx2', (string) Arr::get($settings, 'global.email'));
    }

    /**
     * @test
     */
    public function will_allow_setting_multiple_keyed_settings__for_object()
    {
        settings('notifications-test', 1)
            ->set([
                'global.email'     => 'xxx',
                'orders.new.email' => 'ddd',
            ]);

        settings('privacy-test', 1)
            ->set([
                'section1.111' => 'ppp',
                'section2.ccc' => 'hhh',
            ]);

        $privacySettings = settings('privacy-test')->forUser(1)
                                                   ->get();
        $notificationSettings = settings('notifications-test')->forUser(1)
                                                              ->get();

        $this->assertEquals(Setting::where('user_id', 1)->count(), 1);
        $this->assertEquals('xxx', (string) Arr::get($notificationSettings, 'global.email'));
        $this->assertEquals('ddd', (string) Arr::get($notificationSettings, 'orders.new.email'));
        $this->assertEquals('ppp', (string) Arr::get($privacySettings, 'section1.111'));
        $this->assertEquals('hhh', (string) Arr::get($privacySettings, 'section2.ccc'));
    }

    /**
     * @test
     */
    public function will_throw_exception_if_settings_key_doesnt_exist_as_default__for_object()
    {
        $this->expectException(InvalidSettingsKey::class);
        settings('i-dont-exist', 1)->get();
    }

    /**
     * @test
     */
    public function can_specify_key_in_get_method_to_pluck_out_sub_groups_but_still_have_them_keyed_fully_1__for_object(
    )
    {
        $settings = settings('notifications-test', 1)->get('global');

        $this->assertTrue(Arr::get($settings->all(), 'global.email')->getValue());
        $this->assertTrue(Arr::get($settings->all(), 'global.sms')->getValue());
        $this->assertTrue(Arr::get($settings->all(), 'global.database')->getValue());
    }

    /**
     * @test
     */
    public function can_specify_key_in_get_method_to_pluck_out_sub_groups_but_still_have_them_keyed_fully_2__for_object(
    )
    {
        $settings = settings('notifications-test', 1)->get('orders');

        $this->assertTrue(Arr::get($settings->all(), 'orders.new.email')->getValue());
        $this->assertTrue(Arr::get($settings->all(), 'orders.new.sms')->getValue());
        $this->assertTrue(Arr::get($settings->all(), 'orders.new.database')->getValue());
    }

    /**
     * @test
     */
    public function can_specify_key_in_get_method_to_pluck_out_sub_groups_but_still_have_them_keyed_fully_3__for_object(
    )
    {
        $settings = settings('notifications-test', 1)->get('orders.new');

        $this->assertTrue(Arr::get($settings->all(), 'orders.new.email')->getValue());
        $this->assertTrue(Arr::get($settings->all(), 'orders.new.sms')->getValue());
        $this->assertTrue(Arr::get($settings->all(), 'orders.new.database')->getValue());
    }

    /**
     * @test
     */
    public function can_specify_key_in_get_method_to_pluck_out_sub_groups_but_still_have_them_keyed_fully_4__for_object(
    )
    {
        $settings = settings('privacy-test', 1)->get('section1');

        $this->assertTrue(Arr::get($settings->all(), 'section1.111')->getValue());
        $this->assertTrue(Arr::get($settings->all(), 'section1.222')->getValue());
        $this->assertTrue(Arr::get($settings->all(), 'section1.333')->getValue());
    }

    /**
     * @test
     */
    public function will_merge_in_correct_stored_settings_when_fetching_subset_but_have_other_settings_stored_too()
    {
        settings('notifications-test', 1)
            ->set([
                'global.email'     => 'custom1',
                'orders.new.email' => 'custom2',
            ]);

        $settings = settings('notifications-test', 1)->get('global');
        $this->assertArrayNotHasKey('orders', $settings);
        $this->assertArrayHasKey('global', $settings);
    }
}