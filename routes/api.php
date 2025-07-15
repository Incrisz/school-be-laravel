<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExampleController;

Route::get('/users', [ExampleController::class, 'index']);
Route::post('/users', [ExampleController::class, 'store']);
Route::get('/users/{id}', [ExampleController::class, 'show']);

Route::post('/register-school', [\App\Http\Controllers\SchoolRegistrationController::class, 'register']);

Route::get('/migrate', [\App\Http\Controllers\MigrateController::class, 'migrate']);
