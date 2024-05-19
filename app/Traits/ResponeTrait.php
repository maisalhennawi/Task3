<?php
namespace App\Traits;

trait ResponseTrait
{
    public function successResponse($data, $code = 200)
    {
        return response()->json($data, $code);
    }

    public function errorResponse($message, $code)
    {
        return response()->json(['error' => $message], $code);
    }
}

