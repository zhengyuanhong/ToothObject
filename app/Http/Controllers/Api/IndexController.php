<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ad;
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
        $data['ad'] = Ad::getAd();
        $data['is_admin'] = $request->user('api')->company()->exists();
        return $this->reponseJson(ErrorCode::SUCCESS,$data);
    }
}
