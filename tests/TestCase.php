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
            fn (string $modelName) => 'DcodeGroup\\XeroIntegration\\Database\\Factories\\'.class_basename($modelName).'Factory'
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
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Run the xero_tokens stub migration directly for testing
        $migration = include __DIR__.'/../database/migrations/create_xero_tokens_table.php.stub';
        $migration->up();
    }
}
