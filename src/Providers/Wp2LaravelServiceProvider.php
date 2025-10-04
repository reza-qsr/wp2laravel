<?php

namespace RezaQsr\Wp2Laravel\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use RezaQsr\Wp2Laravel\Contracts\OptionRepositoryInterface;
use RezaQsr\Wp2Laravel\Contracts\PostRepositoryInterface;
use RezaQsr\Wp2Laravel\Contracts\TermRepositoryInterface;
use RezaQsr\Wp2Laravel\Repositories\DbOptionRepository;
use RezaQsr\Wp2Laravel\Repositories\DbPostRepository;
use RezaQsr\Wp2Laravel\Repositories\DBTermRepository;
use RezaQsr\Wp2Laravel\Services\OptionService;
use RezaQsr\Wp2Laravel\Services\PostService;
use RezaQsr\Wp2Laravel\Services\TermService;

class Wp2LaravelServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/wp2laravel.php', 'wp2laravel');

        $this->app->bind(OptionRepositoryInterface::class, DbOptionRepository::class);
        $this->app->singleton(OptionService::class, function($app) {
            return new OptionService($app->make(OptionRepositoryInterface::class));
        });

        $this->app->bind(PostRepositoryInterface::class, DbPostRepository::class);
        $this->app->singleton(PostService::class, function($app) {
            return new PostService($app->make(PostRepositoryInterface::class));
        });

        $this->app->bind(TermRepositoryInterface::class, DBTermRepository::class);
        $this->app->singleton(TermService::class, function($app) {
            return new TermService($app->make(TermRepositoryInterface::class));
        });

        $this->app->singleton('wp2laravel', function($app) {
            return new \RezaQsr\Wp2Laravel\Wp2LaravelManager(
                $app->make(OptionService::class),
                $app->make(PostService::class),
                $app->make(TermService::class)
            );
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
