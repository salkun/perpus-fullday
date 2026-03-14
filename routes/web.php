<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth Routes (session-based, harus di web.php)
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');
Route::get('/user', [AuthController::class, 'user'])->middleware('auth');

/*
|--------------------------------------------------------------------------
| Artisan Commands (Utilitas Hosting)
|--------------------------------------------------------------------------
*/
Route::prefix('artisan')->group(function () {
    Route::get('/migrate', function () {
        \Artisan::call('migrate', ['--force' => true]);
        return 'Migration finished.';
    });

    Route::get('/storage-link', function () {
        \Artisan::call('storage:link');
        return 'Storage link created.';
    });

    Route::get('/optimize', function () {
        \Artisan::call('optimize');
        return 'Configuration and routes cached.';
    });

    Route::get('/optimize-clear', function () {
        \Artisan::call('optimize:clear');
        return 'Caches cleared.';
    });

    Route::get('/key-generate', function () {
        \Artisan::call('key:generate');
        return 'Application key generated.';
    });
});

Route::get('/{any}', fn() => view('app'))->where('any', '.*');
