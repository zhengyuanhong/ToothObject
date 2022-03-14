<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\AppointRecord;
use App\Models\Customer;
use App\Models\DentalCard;
use App\Models\TeethCompany;
use App\Models\WechatUser;
use App\Services\WechatUserService;
use App\Utils\ErrorCode;
use App\Utils\WechatDecode\WXBizDataCrypt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $validator = Validator::make($request->all(), [
            'code' => 'required'
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException('登录失败');
        }

        $code = $request->get('code');
        $app = app('easyWechat');
        $res = $app->auth->session($code);
        if (isset($res['errcode'])) {
            $error = ErrorCode::GET_WECHAT_OPENID_ERR;
            Log::error('获取openid 失败', $error);
            return $this->reponseJson(ErrorCode::GET_WECHAT_OPENID_ERR);
        }

        $data = $this->wechatUserService->createUser($res['openid']);
        $data['session_key'] = $res['session_key'];
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
            'gender' => 'required',
            'company_id' => 'required',
            'sale_user_id' => ''
        ]);

        if ($validator->fails()) {
            return $this->reponseJson(ErrorCode::NO_PARAM);
        }
        $user_id = $request->user('api')->id;

        DB::transaction(function () use ($user_id, $request) {
            //保存用户信息
            $this->wechatUserService->saveUser($user_id, $request->all());
            //领取卡
            DentalCard::drawCard($user_id, $request->all());
            //新建客户
            Customer::create($request->user('api')->id, $request->all());
        });
        return $this->reponseJson(ErrorCode::SUCCESS);
    }

    public function userInfo(Request $request)
    {
        $userInfo = $request->user('api');
        return $this->reponseJson(ErrorCode::SUCCESS, $userInfo->toArray());
    }

    public function cardInfo(Request $request, TeethCompany $teethCompany)
    {
        $card = $teethCompany->cards()->with('user')->where('user_id', $request->user('api')->id)->first();

        $card->appoint_count = AppointRecord::query()
            ->companyAndUser($teethCompany->id, $request->user('api')->id)
            ->where('appoint_status', AppointRecord::STATUS['ARRIVED'])
            ->count();
        return $this->reponseJson(ErrorCode::SUCCESS, $card->toArray());
    }

    public function wechatUserLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException('获取失败');
        }

        $code = $request->get('code');
        $app = app('easyWechat');
        $res = $app->auth->session($code);

        return $this->reponseJson(ErrorCode::SUCCESS, $res);
    }

    public function getPhoneNumberOld(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'session_key' => 'required',
            'iv' => 'required',
            'encryptedData' => 'required',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException('获取失败');
        }

        $user = $request->user('api');

        $pc = new WXBizDataCrypt($user->appid, $input['session_key']);
        $errCode = $pc->decryptData($input['encryptedData'], $input['iv'], $data);
        if ($errCode == 0) {
            $this->reponseJson(ErrorCode::SUCCESS, $data);
        } else {
            $this->reponseJson(ErrorCode::PHONE_FAIL, []);
        }

    }

    public function getPhoneNumber(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'code' => 'required',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException('获取失败');
        }

        $app = app('easyWechat');
        $data = $app->phone_number->getUserPhoneNumber($request->get('code'));
        return $this->reponseJson(ErrorCode::SUCCESS, $data);
    }

    public function otherUserInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException('错误信息');
        }
        $res = WechatUser::query()->with('company')->find($request->get('user_id'));
        if (empty($res)) {
            throw new InvalidRequestException('用户不存在');
        }

        return $this->reponseJson(ErrorCode::SUCCESS, $res->toArray());
    }

    public function updateUserInfo(Request $request){
        $input = $request->all();
        $user = WechatUser::query()->find($request->user('api')->id);

        foreach($input as $key => $value){
            $user->{$key} = $value;
        }
        $user->save();
        return $this->reponseJson(ErrorCode::SUCCESS);
    }
}
