<?php
namespace RezaQsr\Wp2Laravel\Tests;


use Orchestra\Testbench\TestCase as Orchestra;
use RezaQsr\Wp2Laravel\Providers\Wp2LaravelServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [Wp2LaravelServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'mysql');
        config()->set('database.connections.mysql', [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'ecom'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
        ]);


    }
}
