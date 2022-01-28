<?php

namespace App\Http\Controllers;

use App\Utils\ErrorCode;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function reponseJson($errorCode,Array $data = []){
        return response()->json([
            'code'=>$errorCode['code'],
            'message'=>$errorCode['message'],
            'data'=>$data,
            'time'=>time()
        ]);
    }
}
