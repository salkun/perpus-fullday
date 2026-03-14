<?php

use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\VisitorController;
use App\Http\Controllers\Api\VisitLogController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (tanpa auth)
|--------------------------------------------------------------------------
*/
/*
|--------------------------------------------------------------------------
| Books — Public
|--------------------------------------------------------------------------
*/
Route::get('/books',            [BookController::class, 'index']);
Route::get('/books/categories', [BookController::class, 'categories']);
Route::get('/books/{book}',     [BookController::class, 'show']);

/*
|--------------------------------------------------------------------------
| Books — Admin
|--------------------------------------------------------------------------
*/
Route::post('/books',          [BookController::class, 'store'])->middleware('auth');
Route::post('/books/{book}',   [BookController::class, 'update'])->middleware('auth');
Route::delete('/books/{book}', [BookController::class, 'destroy'])->middleware('auth');

/*
|--------------------------------------------------------------------------
| Visitors — Admin
|--------------------------------------------------------------------------
*/
Route::apiResource('/visitors', VisitorController::class)->middleware('auth');

/*
|--------------------------------------------------------------------------
| Visit Logs — Admin
|--------------------------------------------------------------------------
*/
Route::get('/visit-logs/summary', [VisitLogController::class, 'summary'])->middleware('auth');
Route::apiResource('/visit-logs', VisitLogController::class)->middleware('auth');
