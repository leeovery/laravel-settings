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
    'orders'    => [
        'general'        => [
            'email'  => true,
            'alerts' => true,
        ],
        'status_change' => [
            'email'  => true,
            'alerts' => true,
        ],
    ],
];
```

You can do this to fetch the settings:

``` php
$userId = 100;
$settings = settings('user-notifications', $userId)->get();
```

That will return all the above settings for user with the ID of 100. The settings won't be different from the defaults as at this point the user has not made any changes.

Lets change that now...

``` php
settings('user-notifications', $userId)->set(['orders.general.email' => false]);
```

The line above calls set() and passes the key and value. That value is persisted in the DB for that user. So that we can do the following...

``` php
$settings = settings('user-notifications', $userId)->get();
```

$settings above will now return all the default values EXCEPT for the nested key `orders.general.email` where that will equal FALSE, as per the custom change above.


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
