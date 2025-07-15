<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register-school', [\App\Http\Controllers\SchoolRegistrationController::class, 'register']);

Route::get('/migrate', [\App\Http\Controllers\MigrateController::class, 'migrate']);
