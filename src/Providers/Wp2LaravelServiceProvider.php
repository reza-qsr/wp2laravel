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

        $this->mergeConfigFrom(__DIR__ . '/../../config/wp2laravel.php', 'wp2laravel');
        $this->app->bind(OptionRepositoryInterface::class, DbOptionRepository::class);
        $this->app->singleton(OptionService::class, function($app) {
            return new OptionService($app->make(OptionRepositoryInterface::class));
        });

        $this->app->singleton('wp2laravel', function($app) {
            return new Wp2LaravelManager($app->make(OptionService::class));
        });

        AliasLoader::getInstance()->alias('Wp2Laravel', \RezaQsr\Wp2Laravel\Facades\Wp2Laravel::class);
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
