# A Package for the integration of Laravel with Xero

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dcode-group/xero-integration.svg?style=flat-square)](https://packagist.org/packages/dcode-group/xero-integration)
[![GitHub Tests Action Status](https://github.com/spatie/package-xero-integration-laravel/actions/workflows/run-tests.yml/badge.svg)](https://github.com/dcode-group/xero-integration/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://github.com/spatie/package-xero-integration-laravel/actions/workflows/fix-php-code-style-issues.yml/badge.svg)](https://github.com/dcode-group/xero-integration/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/dcode-group/xero-integration.svg?style=flat-square)](https://packagist.org/packages/dcode-group/xero-integration)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require dcode-group/xero-integration
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="xero-integration-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="xero-integration-config"
```

This is the contents of the published config file:

```php
return [
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="xero-integration-views"
```

## Usage

```php
$xeroIntegration = new DcodeGroup\XeroIntegration();
echo $xeroIntegration->echoPhrase('Hello, DcodeGroup!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

If believe there is a valnerability in this package please send us an email at [forge@dcodegroup.com](mailto:forge@dcodegroup.com)

## Credits

- [Dcode Group](https://dcodegroup.com)
- [All Contributors](../../contributors)

Package based on [Spatie's Laravel Skeleton](https://github.com/spatie/package-skeleton-laravel).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
