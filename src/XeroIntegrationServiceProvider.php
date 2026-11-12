<?php

namespace Dcodegroup\XeroIntegration;

use Calcinai\OAuth2\Client\Provider\Xero;
use Dcodegroup\XeroIntegration\Commands\MakeXeroDataCommand;
use Dcodegroup\XeroIntegration\Contracts\XeroDataFinder;
use Dcodegroup\XeroIntegration\Contracts\XeroGateway;
use Dcodegroup\XeroIntegration\Exceptions\XeroConfigException;
use Dcodegroup\XeroIntegration\Services\SaloonXeroGateway;
use Dcodegroup\XeroIntegration\Services\XeroDataFinderService;
use Saloon\Laravel\SaloonServiceProvider;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class XeroIntegrationServiceProvider extends PackageServiceProvider
{
    public function boot()
    {
        parent::boot();

        $this->app->singleton(Xero::class, function () {
            if (empty(config('xero-integration.oauth.client_id'))) {
                report(new XeroConfigException('Xero Client ID is required. Please set the XERO_CLIENT_ID environment variable.'));
            }

            if (empty(config('xero-integration.oauth.client_secret'))) {
                report(new XeroConfigException('Xero Client Secret is required. Please set the XERO_CLIENT_SECRET environment variable.'));
            }

            return new Xero([
                'clientId' => config('xero-integration.oauth.client_id'),
                'clientSecret' => config('xero-integration.oauth.client_secret'),
                'redirectUri' => route('xero.callback'),
            ]);
        });

        $this->app->singleton(XeroApp::class, function () {
            return new XeroApp;
        });

        $this->app->singleton(XeroDataFinder::class, XeroDataFinderService::class);

        $this->app->bind(XeroGateway::class, SaloonXeroGateway::class);
    }

    public function register()
    {
        parent::register();

        $this->app->register(SaloonServiceProvider::class);
    }

    public function configurePackage(Package $package): void
    {
        $package
            ->name('xero-integration')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigrations([
                'create_xero_tokens_table',
                'create_xero_records_table',
                'create_xero_webhooks_table',
                'create_xero_webhook_events_table',
            ])
            ->hasCommand(MakeXeroDataCommand::class)
            ->hasRoute('xero');
    }
}
