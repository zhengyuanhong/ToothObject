<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\DentalCard;
use App\Models\WechatUser;
use App\Services\WechatUserService;
use App\Utils\ErrorCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    protected $wechatUserService = null;

    public function __construct(WechatUserService $wechatUserService)
    {
        $this->wechatUserService = $wechatUserService;
    }

    function register(Request $request)
    {
        $code = $request->get('code');
        $app = app('easyWechat');
        $res = $app->auth->session($code);

        if (isset($res['errcode'])) {
            $error = ErrorCode::GET_WECHAT_OPENID_ERR;
            Log::error('获取openid 失败', $error);
            return $this->reponseJson(ErrorCode::GET_WECHAT_OPENID_ERR);
        }

        $data = $this->wechatUserService->createUser($res['openid']);
        return $this->reponseJson(ErrorCode::SUCCESS, $data);
    }

    function unAuth()
    {
        return $this->reponseJson(ErrorCode::UN_AUTH);
    }

    public function drawCard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'name' => 'required',
            'avatar' => 'required',
            'gender' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->reponseJson(ErrorCode::NO_PARAM);
        }
        $user_id = $request->user('api')->id;
        //保存用户信息
        $this->wechatUserService->saveUser($user_id, $request->all());
        //领取卡
        DentalCard::drawCard($user_id, $request->all());
        //新建客户
        Customer::create($request->all());
        return $this->reponseJson(ErrorCode::SUCCESS);
    }

    public function userInfo(Request $request)
    {
        $userInfo = WechatUser::with('card')->find($request->user('api')->id);
        return $this->reponseJson(ErrorCode::SUCCESS, $userInfo->toArray());
    }

    public function customer()
    {
        $res = auth('api')->user()->customer()->orderBy('created_at','DESC')->paginate(20);
        return CustomerResource::collection($res);
    }
}
