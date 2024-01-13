<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        // Requests under /api are always answered with JSON, even when the
        // client forgot to send an Accept header. Without this a missing
        // record renders Laravel's HTML error page to an API consumer.
        $this->renderable(function (ModelNotFoundException $e, Request $request): ?JsonResponse {
            return $request->is('api/*')
                ? response()->json(['message' => 'Resource not found.'], JsonResponse::HTTP_NOT_FOUND)
                : null;
        });

        $this->renderable(function (NotFoundHttpException $e, Request $request): ?JsonResponse {
            return $request->is('api/*')
                ? response()->json(['message' => 'Resource not found.'], JsonResponse::HTTP_NOT_FOUND)
                : null;
        });
    }
}
