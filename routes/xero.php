<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => config('xero-integration.path'),
    'middleware' => config('xero-integration.middleware'),
    'as' => 'xero.',
], function () {
    if (config('xero-integration.routes.controllers.index')) {
        Route::get('/', config('xero-integration.routes.controllers.index'))->name('index');
    }
    Route::get('/auth', config('xero-integration.routes.controllers.auth'))->name('auth');
    Route::get('/callback', config('xero-integration.routes.controllers.callback'))
        ->withoutMiddleware(config('xero-integration.routes.exclude_middleware_for_callback'))
        ->name('callback');
});
