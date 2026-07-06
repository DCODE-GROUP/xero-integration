<?php

use DcodeGroup\XeroIntegration\Http\Controllers\XeroAuthController;
use DcodeGroup\XeroIntegration\Http\Controllers\XeroCallbackController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'Xero Integration Workbench',
        'routes' => [
            'auth' => '/xero/auth',
            'callback' => '/xero/callback',
        ],
    ]);
});

// Test routes for Xero Integration
Route::get('/xero/auth', XeroAuthController::class)->name('xero.auth');
Route::get('/xero/callback', XeroCallbackController::class)->name('xero.callback');

// Dashboard route (callback success)
Route::get('/dashboard', function () {
    return response()->json([
        'message' => 'Successfully authenticated with Xero!',
    ]);
})->name('dashboard');
