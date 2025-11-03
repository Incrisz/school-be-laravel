<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;
use Illuminate\Support\Arr;


class Handler extends ExceptionHandler
{
    // …

    public function render(Request $request, Throwable $e): JsonResponse|\Illuminate\Http\Response
    {
        // If this is an API call (prefix api/*) or the client expects JSON...
        if ($request->is('api/*') || $request->expectsJson()) {

            // Determine status
            $status = $e instanceof HttpExceptionInterface
                ? $e->getStatusCode()
                : 500;

            // Base payload
            $payload = [
                'message' => $status === 500 && !config('app.debug')
                    ? 'Server Error'
                    : $e->getMessage(),
            ];

            // In debug mode, add exception class & trace
            if (config('app.debug')) {
                $payload['exception'] = get_class($e);
                $payload['trace']     = collect($e->getTrace())->map(fn($frame) => Arr::except($frame, ['args']))->all();
            }

            return response()->json($payload, $status);
        }

        // Otherwise, fall back to the normal HTML error page
        return parent::render($request, $e);
    }
}
