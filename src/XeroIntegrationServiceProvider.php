<?php

namespace DcodeGroup\XeroIntegration;

use DcodeGroup\XeroIntegration\Commands\MakeXeroDataCommand;
use DcodeGroup\XeroIntegration\Exceptions\XeroIntegrationException;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class XeroIntegrationServiceProvider extends PackageServiceProvider
{
    public function register()
    {
        parent::register();

        $webhookSecret = config('xero-integration.webhooks.secret');

        if (empty($webhookSecret)) {
            report(new XeroIntegrationException('Xero webhook secret is not configured. Please set the XERO_WEBHOOK_SECRET environment variable.'));

            return;
        }

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
            ->hasMigrations(['create_xero_tokens_table', 'create_xero_record_table'])
            ->hasCommand(MakeXeroDataCommand::class)
            ->hasRoute('xero');
    }
}
