<?php

namespace Dcodegroup\XeroIntegration;

use Calcinai\OAuth2\Client\Provider\Xero;
use Dcodegroup\XeroIntegration\Commands\MakeXeroDataCommand;
use Dcodegroup\XeroIntegration\Exceptions\XeroConfigException;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class XeroIntegrationServiceProvider extends PackageServiceProvider
{
    public function boot()
    {
        parent::boot();

        if (empty(config('xero-integration.oauth.client_id'))) {
            report(new XeroConfigException('Xero Client ID is required. Please set the XERO_CLIENT_ID environment variable.'));
        }

        if (empty(config('xero-integration.oauth.client_secret'))) {
            report(new XeroConfigException('Xero Client Secret is required. Please set the XERO_CLIENT_SECRET environment variable.'));
        }

        $this->app->singleton(Xero::class, function () {
            return new Xero([
                'clientId' => config('xero-integration.oauth.client_id'),
                'clientSecret' => config('xero-integration.oauth.client_secret'),
                'redirectUri' => route('xero.callback'),
            ]);
        });

        if (empty(config('xero-integration.webhooks.secret'))) {
            report(new XeroConfigException('Xero webhook secret is not configured. Please set the XERO_WEBHOOK_SECRET environment variable.'));
        }

        $this->app->singleton(XeroApp::class, function () {
            return new XeroApp;
        });
    }

    public function register()
    {
        parent::register();
    }

    public function configurePackage(Package $package): void
    {
        $package
            ->name('xero-integration')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigrations(['create_xero_tokens_table', 'create_xero_records_table'])
            ->hasCommand(MakeXeroDataCommand::class)
            ->hasRoute('xero');
    }
}
