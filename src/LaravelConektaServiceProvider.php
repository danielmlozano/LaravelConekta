<?php

namespace Danielmlozano\LaravelConekta;

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
        //
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
}
