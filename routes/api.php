<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\V1\SchoolRegistrationController;

Route::post('/register-school', [SchoolRegistrationController::class, 'register']);

Route::get('/migrate', [\App\Http\Controllers\MigrateController::class, 'migrate']);
