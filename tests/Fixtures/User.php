<?php

namespace Danielmlozano\LaravelConekta\Tests\Fixtures;

use Illuminate\Foundation\Auth\User as Model;
use Danielmlozano\LaravelConekta\Purchaser;

class User extends Model
{
    use Purchaser;
}
