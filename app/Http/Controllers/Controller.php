<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="School API",
 *      description="API for managing school data"
 * )
 * @OA\Tag(
 *     name="school-v1.0",
 *     description="API Endpoints for School v1.0"
 * )
 * @OA\Tag(
 *     name="school-v1.1",
 *     description="API Endpoints for School v1.1"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}