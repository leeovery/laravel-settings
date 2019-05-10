# Laravel Settings

[![Latest Version on Packagist](https://img.shields.io/packagist/v/leeovery/laravel-settings.svg?style=flat-square)](https://packagist.org/packages/leeovery/laravel-settings)
[![Build Status](https://img.shields.io/travis/leeovery/laravel-settings/master.svg?style=flat-square)](https://travis-ci.org/leeovery/laravel-settings)
[![Quality Score](https://img.shields.io/scrutinizer/g/leeovery/laravel-settings.svg?style=flat-square)](https://scrutinizer-ci.com/g/leeovery/laravel-settings)
[![Total Downloads](https://img.shields.io/packagist/dt/leeovery/laravel-settings.svg?style=flat-square)](https://packagist.org/packages/leeovery/laravel-settings)

This package allows you to create one or many setting files which store default settings, but also expose an API to edit the settings, and store those edits in the DB. When the settings are fetched the custom values are merged into the defaults.

Most useful for user-based settings, but can be used for a multitude of other reasons.

Best to give a quick example...

## Installation

You can install the package via composer:

```bash
composer require leeovery/laravel-settings
```

## Usage

Given a settings file called `/config/settings-user-notifications.php`
and with the following contents...

``` php

return [
    'orders' => [
        'general' => [
            'email'  => true,
            'alerts' => true,
        ],
        'status_change' => [
            'email'  => true,
            'alerts' => true,
        ],
    ],
    ...
];

```

You can do this to fetch the settings:

``` php

$userId = 100;

settings('user-notifications', $userId)->get();

// will return...
'orders' => [
    'general' => [
        'email'  => true,
        'alerts' => true,
    ],
    'status_change' => [
        'email'  => true,
        'alerts' => true,
    ],
],
...

```

As you can see, this will return all the above settings for user with the ID of 100.

Note that those settings won't be different from the defaults as at this point the user has not made any changes.

Lets change that now...

``` php

settings('user-notifications', $userId)->set(['orders.general.email' => false]);

```

The line above calls set() and passes the key and value. That value is persisted in the DB for that user. So that we can do the following...

``` php

settings('user-notifications', $userId)->get();

// will return...
'orders' => [
    'general' => [
        'email'  => false, // NOTE this is different!
        'alerts' => true,
    ],
    'status_change' => [
        'email'  => true,
        'alerts' => true,
    ],
],
...

```

`$settings` above will now equal all the default values EXCEPT for the nested key `orders.general.email` where that will equal FALSE, as per the custom change above.

That's the most basic use-case...

But, you can do more...

## Multiple Setting Files

You can setup multiple setting files in the config directory, just prepend the filename with `settings-` (or whatever you configure in the package config file.

Eg:

`/config/settings-user-privacy.php`
`/config/settings-social.php`
`/config/settings-user-account-access.php`

Now you can `get()` and `set()` the settings from those files...

``` php

$settings = settings('user-privacy', $userId)->get();
$settings = settings('social', $userId)->get();

```

## Value Object Based Values

You can use objects as the values in the settings file, like this:

``` php

return [
    'orders' => [
        'general' => [
            'email'  => SettingStore::make(true, 'You can set a label here'), // option one
            'alerts' => LaravelSettings::setting(true, 'You can set a label here'), // option two
        ],
    ],
    ...
];

```

## Access Subsets Rather Than All Settings

Sometimes you only need a subset of settings. You can do that by specifiying the key with dot notation. Note the package will still fully key the results so that you can use the keys for setting.

Eg:

Given a setting file `/config/settings-email-notifications.php`:

``` php

return [
    'orders' => [
        'general' => [
            'email'  => SettingStore::make(true),
            'alerts' => SettingStore::make(true),
        ],
        'digital' => [
            'status_change' => [
                'email'  => SettingStore::make(true),
                'alerts' => SettingStore::make(true),
            ],
        ],
    ],
    ...
];

```

You can store a user edit like this:

``` php

settings('user-notifications', $userId)->set([
    'orders.digital.status_change.email' => false // just store primitive value here not VO
]);

```

And... you can access the deep `status_change` subset like follows:

``` php

settings('email-notifications', $userId)->get('orders.digital.status_change');

// will return...
'orders' => [
    'digital' => [
        'status_change' => [
            'email'  => false, // note non-default value
            'alerts' => true,
        ],
    ],
],

```

As you can see we fully key the results but only return the requested subset.


### TODO

Caching
Default in DB or file (driver system)
SettingStore value validation

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Lee Overy](https://github.com/leeovery)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
