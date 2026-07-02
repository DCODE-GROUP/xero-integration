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
