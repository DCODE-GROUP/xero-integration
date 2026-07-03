# A Package for the integration of Laravel with Xero

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dcode-group/xero-integration.svg?style=flat-square)](https://packagist.org/packages/dcode-group/xero-integration)
[![GitHub Tests Action Status](https://github.com/DCODE-GROUP/xero-integration/actions/workflows/run-tests.yml/badge.svg)](https://github.com/DCODE-GROUP/xero-integration/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://github.com/DCODE-GROUP/xero-integration/actions/workflows/fix-php-code-style-issues.yml/badge.svg)](https://github.com/DCODE-GROUP/xero-integration/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/dcode-group/xero-integration.svg?style=flat-square)](https://packagist.org/packages/dcode-group/xero-integration)

An opininated Xero integration package for Laravel that allows for fluent quering of Xero records and mapping of Xero Entities

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

## Usage

```php
XeroIntegration::{Model}->{Query}->get();
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
- [Josh Young](https://jny986.com)

Package based on [Spatie's Laravel Skeleton](https://github.com/spatie/package-skeleton-laravel).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
