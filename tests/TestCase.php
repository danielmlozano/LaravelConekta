<?php

namespace Danielmlozano\LaravelConekta\Tests;

use Danielmlozano\LaravelConekta\LaravelConektaServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            LaravelConektaServiceProvider::class,
        ];
    }
}
