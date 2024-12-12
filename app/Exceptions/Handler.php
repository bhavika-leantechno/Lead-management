<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

     /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable $exception
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $exception)
    {
        // Handle UnauthorizedHttpException (e.g., expired tokens)
        if ($exception instanceof UnauthorizedHttpException) {
            return response()->json([
                'status' => false,
                'message' => 'Authentication token has expired or is invalid. Please log in again.',
            ], 401);
        }

        // Handle AuthenticationException
        if ($exception instanceof AuthenticationException) {
            return response()->json([
                'status' => false,
                'message' => 'User is not authenticated. Please log in.',
            ], 401);
        }

        // Handle other exceptions using the parent method
        return parent::render($request, $exception);
    }
}
