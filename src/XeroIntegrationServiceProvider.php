<?php

namespace DcodeGroup\XeroIntegration;

use DcodeGroup\XeroIntegration\Http\Connectors\XeroConnector;
use Saloon\Http\Senders\GuzzleSender;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class XeroIntegrationServiceProvider extends PackageServiceProvider
{
    public function register()
    {
        parent::register();

        $this->app->singleton(GuzzleSender::class, fn () => new GuzzleSender);

        $this->app->singleton(XeroConnector::class, fn () => new XeroConnector);
    }

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
            ->hasMigration('create_xero_tokens_table')
            ->hasRoute('xero');
    }
}
