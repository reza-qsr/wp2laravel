<?php

namespace RezaQsr\Wp2Laravel\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use RezaQsr\Wp2Laravel\Contracts\OptionRepositoryInterface;
use RezaQsr\Wp2Laravel\Repositories\DbOptionRepository;
use RezaQsr\Wp2Laravel\Services\OptionService;
use RezaQsr\Wp2Laravel\Wp2LaravelManager;

class Wp2LaravelServiceProvider extends ServiceProvider
{
    public function register()
    {
        // merge config defaults from package to app config
        $this->mergeConfigFrom(__DIR__ . '/../../config/wp2laravel.php', 'wp2laravel');

        // bind interface to DB implementation
        $this->app->bind(OptionRepositoryInterface::class, function($app) {
            return new DbOptionRepository();
        });

        $this->app->singleton(OptionService::class, function($app) {
            return new OptionService($app->make(OptionRepositoryInterface::class));
        });

        $this->app->singleton(Wp2LaravelManager::class, function($app) {
            return new Wp2LaravelManager($app->make(OptionService::class));
        });

        // use Wp2Laravel
        AliasLoader::getInstance()->alias('Wp2Laravel', \RezaQsr\Wp2Laravel\Facades\Wp2Laravel::class); // تعریف alias
    }

    public function boot()
    {

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/wp2laravel.php' => config_path('wp2laravel.php'),
            ], 'wp2laravel-config');
        }
    }
}
