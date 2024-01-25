<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Throwable;

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

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => true,
                    'message' => $e->getMessage(),
                    'data' => null,
                ], 401);
            } else {
                return redirect()->route('login')->with('message', 'You must login first!');
            }
        });

        $this->renderable(function (UnauthorizedException $e, $request) {
            return redirect()->route('login')->with('message', 'You must login first!');
        });

        $this->renderable(function (AccessDeniedHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => true,
                    'message' => $e->getMessage(),
                    'data' => null,
                ], $e->getStatusCode());
            } else {
                return abort($e->getStatusCode(), $e->getMessage());
            }
        });

        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
