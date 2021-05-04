<?php

namespace Danielmlozano\LaravelConekta;

use Conekta\Conekta;
use Illuminate\Support\Facades\App;

class LaravelConekta
{

    /**
     * The Laravel Conekta version
     *
     * @var string
     */
    const VERSION = "1.0";

    /**
     * Tje Conekta API version
     *
     * @var string
     */
    const CONEKTA_VERSION = '2.0.0';

    /**
     * Init the Conekta configuration
     *
     * @static
     * @return void
     */
    public static function init()
    {
        if (App::environment() !== 'testing') {
            Conekta::setApiKey(config('conekta.secret'));
            Conekta::setApiVersion(static::CONEKTA_VERSION);
            Conekta::setLocale(App::currentLocale());
        }
    }
}
