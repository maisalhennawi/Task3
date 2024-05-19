<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class Handler extends ExceptionHandler
{
    protected $dontReport = [];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (HttpExceptionInterface $e, $request) {
            return $this->renderHttpException($e);
        });

        $this->renderable(function (ModelNotFoundException $e, $request) {
            return $this->renderModelNotFoundException($request, $e);
        });

        $this->renderable(function (ValidationException $e, $request) {
            return $this->renderValidationException($request, $e);
        });

        $this->renderable(function (QueryException $e, $request) {
            return $this->renderQueryException($request, $e);
        });

        // $this->renderable(function (Throwable $e, $request) {
        //     return $this->handleThrowable($request, $e);
        // });
    }

    protected function renderHttpException(HttpExceptionInterface $e)
    {
        $status = $e->getStatusCode();
        $message = $e->getMessage() ?: $this->getHttpStatusMessage($status);

        return response()->json(['error' => $message], $status);
    }

    protected function renderModelNotFoundException(Request $request, ModelNotFoundException $e)
    {
        return response()->json(['error' => 'Record not found'], 404);
    }

    protected function renderValidationException(Request $request, ValidationException $e)
    {
        return response()->json([
            'error' => 'Validation error',
            'errors' => $e->errors(),
        ], 422);
    }

    protected function renderQueryException(Request $request, QueryException $e)
    {
        if (strpos($e->getMessage(), 'for key') !== false) {
            return response()->json([
                'error' => 'Unique constraint violation',
                'message' => $e->getMessage(),
            ], 409);
        }

        return response()->json([
            'error' => 'Database error',
            'message' => $e->getMessage(),
        ], 500);
    }

    private function getHttpStatusMessage($status)
    {
        $messages = [
            401 => 'Unauthorized',
            404 => 'Not Found',
            422 => 'Unprocessable Entity',
        ];

        return $messages[$status] ?? 'An error occurred';
    }

    // protected function handleThrowable(Request $request, Throwable $e)
    // {
    //     // Custom handling for other throwables if needed
    //     return $this->render($request, $e);
    // }
}
