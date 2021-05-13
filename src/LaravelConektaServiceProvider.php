<?php

namespace Danielmlozano\LaravelConekta;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class LaravelConektaServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any package services.
     *
     * @access public
     * @return void
     */
    public function boot()
    {
        $this->registerMigrations();
        $this->registerRoutes();
        $this->registerPublishing();
    }

    /**
     * Registers any application services
     *
     * @access public
     * @return void
     */
    public function register()
    {
        $this->configure();
    }

    /**
     * Setup the configuration for Conekta
     *
     * @return void
     */
    protected function configure()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/conekta.php',
            'conekta',
        );
    }

    /**
     * Register the package migrations.
     *
     * @return void
     */
    protected function registerMigrations()
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }
    }

    /**
     * Register the package routes.
     *
     * @return void
     */
    protected function registerRoutes()
    {
        Route::group([
            'namespace' => 'Danielmlozano\LaravelConekta\Http\Controllers',
            'as' => 'conekta.',
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        });
    }

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    protected function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/conekta.php' => $this->app->configPath('conekta.php'),
            ], 'conekta-config');

            $this->publishes([
                __DIR__.'/../database/migrations' => $this->app->databasePath('migrations'),
            ], 'conekta-migrations');
        }
    }
}
