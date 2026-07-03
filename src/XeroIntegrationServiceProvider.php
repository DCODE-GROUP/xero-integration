<?php

namespace DcodeGroup\XeroIntegration;

use DcodeGroup\XeroIntegration\Commands\MakeXeroDataCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class XeroIntegrationServiceProvider extends PackageServiceProvider
{
    public function register()
    {
        parent::register();

        $this->app->singleton(XeroApp::class, function () {
            return new XeroApp;
        });
    }

    public function configurePackage(Package $package): void
    {
        $package
            ->name('xero-integration')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_xero_tokens_table')
            ->hasCommand(MakeXeroDataCommand::class)
            ->hasRoute('xero');
    }
}
