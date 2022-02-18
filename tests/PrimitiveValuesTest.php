<?php

namespace Leeovery\LaravelSettings\Tests;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Leeovery\LaravelSettings\Exceptions\InvalidSettingsKey;
use Leeovery\LaravelSettings\LaravelSettings;
use Leeovery\LaravelSettings\Setting;

class PrimitiveValuesTest extends TestCase
{
    public $notificationSettings;

    public $privacySettings;

    public function setUp(): void
    {
        parent::setUp();

        Config::set('settings-notifications-test', $this->notificationSettings = [
            'global' => [
                'email'    => true,
                'sms'      => true,
                'database' => true,
            ],
            'orders' => [
                'new' => [
                    'email'    => true,
                    'sms'      => true,
                    'database' => true,
                ],
            ],
        ]);

        Config::set('settings-privacy-test', $this->privacySettings = [
            'section1' => [
                '111' => true,
                '222' => true,
                '333' => true,
            ],
            'section2' => [
                'aaa' => true,
                'bbb' => true,
                'ccc' => true,
            ],
        ]);
    }

    /**
     * @test
     */
    public function can_get_settings_object_using_helper_with_no_parameters()
    {
        $this->assertInstanceOf(LaravelSettings::class, settings());
    }

    /**
     * @test
     */
    public function can_get_settings_without_user_id()
    {
        $settings = settings('notifications-test')->get();
        $this->assertEquals($settings->all(), $this->notificationSettings);
    }

    /**
     * @test
     */
    public function can_get_settings_for_user()
    {
        $settings = settings('notifications-test')->forUser(1)
            ->get();
        $this->assertEquals($settings->all(), $this->notificationSettings);
    }

    /**
     * @test
     */
    public function can_set_settings_for_user()
    {
        settings('notifications-test', 1)
            ->set([
                'global.email'     => '123',
                'orders.new.email' => '456',
            ]);

        $settings = settings('notifications-test')->forUser(1)
            ->get();

        $this->assertEquals(Setting::where('user_id', 1)->count(), 1);
        $this->assertEquals('123', Arr::get($settings, 'global.email'));
        $this->assertEquals('456', Arr::get($settings, 'orders.new.email'));
        $this->assertEquals(true, Arr::get($settings, 'orders.new.sms'));
    }

    /**
     * @test
     */
    public function will_delete_custom_settings_for_user_when_they_have_changed_the_last_one()
    {
        settings('notifications-test', 1)
            ->set([
                'global.email' => 'vvv',
            ]);

        $settings = settings('notifications-test')->forUser(1)
            ->get();

        $this->assertEquals(Setting::where('user_id', 1)->count(), 1);
        $this->assertEquals('vvv', Arr::get($settings, 'global.email'));

        // set again to match default...
        settings('notifications-test', 1)
            ->set([
                'global.email' => true,
            ]);

        $settings = settings('notifications-test', 1)->get();

        $this->assertEquals(true, Arr::get($settings, 'global.email'));
        $this->assertEquals(Setting::where('user_id', 1)->count(), 0);
    }

    /**
     * @test
     */
    public function will_ignore_passed_new_settings_if_they_do_not_exist_in_default()
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
        $this->assertEquals('xxx2', Arr::get($settings, 'global.email'));
    }

    /**
     * @test
     */
    public function will_allow_setting_multiple_keyed_settings()
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
        $this->assertEquals('xxx', Arr::get($notificationSettings, 'global.email'));
        $this->assertEquals('ddd', Arr::get($notificationSettings, 'orders.new.email'));
        $this->assertEquals('ppp', Arr::get($privacySettings, 'section1.111'));
        $this->assertEquals('hhh', Arr::get($privacySettings, 'section2.ccc'));
    }

    /**
     * @test
     */
    public function will_throw_exception_if_settings_key_doesnt_exist_as_default()
    {
        $this->expectException(InvalidSettingsKey::class);
        settings('i-dont-exist', 1)->get();
    }

    /**
     * @test
     */
    public function can_specify_key_in_get_method_to_pluck_out_sub_groups_but_still_have_them_keyed_fully_1()
    {
        $settings = settings('notifications-test', 1)->get('global');

        $this->assertEquals($settings->all(), [
            'global' => [
                'email'    => true,
                'sms'      => true,
                'database' => true,
            ],
        ]);
    }

    /**
     * @test
     */
    public function can_specify_key_in_get_method_to_pluck_out_sub_groups_but_still_have_them_keyed_fully_2()
    {
        $settings = settings('notifications-test', 1)->get('orders');

        $this->assertEquals($settings->all(), [
            'orders' => [
                'new' => [
                    'email'    => true,
                    'sms'      => true,
                    'database' => true,
                ],
            ],
        ]);
    }

    /**
     * @test
     */
    public function can_specify_key_in_get_method_to_pluck_out_sub_groups_but_still_have_them_keyed_fully_3()
    {
        $settings = settings('notifications-test', 1)->get('orders.new');

        $this->assertEquals($settings->all(), [
            'orders' => [
                'new' => [
                    'email'    => true,
                    'sms'      => true,
                    'database' => true,
                ],
            ],
        ]);
    }

    /**
     * @test
     */
    public function can_specify_key_in_get_method_to_pluck_out_sub_groups_but_still_have_them_keyed_fully_4()
    {
        $settings = settings('privacy-test', 1)->get('section1');

        $this->assertEquals($settings->all(), [
            'section1' => [
                '111' => true,
                '222' => true,
                '333' => true,
            ],
        ]);
    }

    /**
     * @test
     */
    public function ensure_can_fetch_using_subset_when_expecting_non_default_values()
    {
        settings('notifications-test', 1)
            ->set([
                'orders.new.email' => 'custom2',
            ]);

        $settings = settings('notifications-test', 1)->get('orders.new')->all();

        $this->assertEquals('custom2', Arr::get($settings, 'orders.new.email'));
    }
}
