<?php

namespace App;

trait HttpResponse
{
    protected function success($data, string $message = null, int $code = 200)
        {
            return response()->json([
                'status' => 'Request was successful.',
                'message' => $message,
                'data' => $data
            ], $code);
        }
        
        protected function error($data, string $message, int $code)
        {
            return response()->json([
                'status' => 'Congrats, you found a mythical error...',
                'message' => $message,
                'data' => $data
            ], $code);
        }
}
