<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Utils\ErrorCode;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function search(Request $request){
        $key_world = $request->get('key_world','');
        if(empty($key_world)) throw new InvalidRequestException('请输入关键词');
        return $this->reponseJson(ErrorCode::SUCCESS,Customer::search($key_world));
    }
}
