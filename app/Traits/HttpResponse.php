<?php

namespace App\Traits;

trait HttpResponse
{
    public function sendResponse($data = [], $message = [], $code = 200)
    {
        return response()->json([
            'status' => $code,
            'message' => $message,
            'data' => $data
        ], $code);
    }
}
