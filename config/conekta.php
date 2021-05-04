<?php

return [
/*
    |--------------------------------------------------------------------------
    | Conekta Keys
    |--------------------------------------------------------------------------
    |
    | The Conekta public key and secret key give you access to Conekta's API.
    |
    */

    'key' => env('CONEKTA_KEY'),
    'secret' => env('CONEKTA_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    |
    | This is the model in your application that implements the Purchaser trait
    | provided by the package. It will serve as the primary model you use while
    | interacting with Conekta related methods, payments, and so on.
    |
    */

    'model' => env('CONEKTA_USER_MODEL', class_exists(App\Models\User::class) ? App\Models\User::class : App\User::class),

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    |
    | This is the default currency that will be used when generating charges
    | from your application.
    |
    */

    'currency' => env('CONEKTA_CURRENCY', 'usd'),

];
