<?php

use Illuminate\Support\Facades\Route;

Route::post(config('conekta.webhook'), 'WebhookController@handleWebhook')->name('webhook');
