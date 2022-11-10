<?php

    namespace App\Traits;


    trait ApiResponser
    {
        protected function successResponse($data, $code, $message = null)
        {
            return response()->json(
                [
                    'status' => 'success',
                    'message' => $message,
                    'data' => $data,
                    'code' => $code
    
                ]
            );
        }
    
        protected function errorResponse($message,$code,$data=null)
        {
            return response()->json(
                [
                    'status'=>'Failed',
                    'message'=>$message,
                    'data'=>$data,
                    'code'=>$code
                ]
            );
        }
    }

