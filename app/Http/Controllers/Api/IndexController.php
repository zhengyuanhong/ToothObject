<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppointRecord;
use App\Models\TeethCompany;
use App\Models\WechatUser;
use App\Services\WechatUserService;
use App\Utils\ErrorCode;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    protected $wechatUserService = null;
    public function __construct(WechatUserService $wechatUserService)
    {
        $this->wechatUserService = $wechatUserService;
    }

    public function index(Request $request){
        $data['notify'] = $this->wechatUserService->appointNotify();
        $data['company'] = TeethCompany::oneCompany();
        return $this->reponseJson(ErrorCode::SUCCESS,$data);
    }
}
