<?php

namespace App\Classes;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class ApiResponseClass
{
    public static function rollback($e, $message ="Something went wrong! Process not completed. Rollback."){
        DB::rollBack();
        self::throw($e, $message);
    }

    public static function throw($e, $message ="Something went wrong! Process not completed"){
        Log::info($e);
        throw new HttpResponseException(response()->json(["message"=> $message], 500));
    }

    public static function sendResponse($result, $message, $code=200, $success=true){
        $response=[
            'success' => $success,
            'data'    => $result
        ];
        if(!empty($message)){
            $response['message'] =$message;
        }
        return response()->json($response, $code);
    }

    public static function sendTableResponse($total, $rows, $message, $code=200, $success=true){
        $response=[
            'success' => $success,
            'data'    => [
                'total' => $total,
                //'totalFiltered' => $totalFiltered,
                'rows' => $rows
                ]
        ];
        if(!empty($message)){
            $response['message'] =$message;
        }
        return response()->json($response, $code);
    }

}