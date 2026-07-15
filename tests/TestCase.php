<?php

namespace DcodeGroup\XeroIntegration\Tests;

use DcodeGroup\XeroIntegration\XeroIntegrationServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => match (true) {
                str_starts_with($modelName, 'Workbench\\') => 'Workbench\\Database\\Factories\\'.class_basename($modelName).'Factory',
                default => 'DcodeGroup\\XeroIntegration\\Database\\Factories\\'.class_basename($modelName).'Factory'
            }
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            XeroIntegrationServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        config()->set('xero-integration.routes.callback_success_route', 'dashboard');
        config()->set('app.key', 'base64:AckfSECXIvnK5r28GVIWUAxmbBSjTsmFVb/gGnlNyNE=');
        config()->set('app.cipher', 'AES-256-CBC');

        // Configure tenancy model so migrations can create tenant_id column
        config()->set('xero-integration.tenancy.model', 'Workbench\\App\\Models\\Tenant');
    }

    protected function defineDatabaseMigrations()
    {
        $migrationsPath = __DIR__.'/../database/migrations';
        $this->loadMigrationsFrom($migrationsPath);

        // Run the xero_tokens stub migration directly for testing
        $extensions = ['stub'];

        foreach (\File::allFiles($migrationsPath) as $migration) {
            if (in_array($migration->getExtension(), $extensions, true)) {
                (include $migration->getRealPath())->up();
            }
        }
    }
}
