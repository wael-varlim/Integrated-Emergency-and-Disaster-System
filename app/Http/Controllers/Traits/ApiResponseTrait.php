<?php

namespace App\Http\Controllers\Traits;

trait ApiResponseTrait
{
    public function apiResponse($data = null, $msg = null, $status = null)
    {
        $array = [
            'data' => $data,
            'message' => $msg,
            'status' => $status
        ];

        return response()->json($array, $status);
    }


}
