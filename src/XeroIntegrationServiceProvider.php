<?php

namespace DcodeGroup\XeroIntegration;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use DcodeGroup\XeroIntegration\Commands\XeroIntegrationCommand;

class XeroIntegrationServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('xero-integration')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_xero_integration_table')
            ->hasCommand(XeroIntegrationCommand::class);
    }
}
