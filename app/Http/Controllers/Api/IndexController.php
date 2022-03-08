<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\AppointRecord;
use App\Models\DentalCard;
use App\Models\TeethCompany;
use App\Models\WechatUser;
use App\Services\TeethCompanyService;
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

    public function index(Request $request)
    {
        $id = $request->get('company_id');
        if (empty($id)) throw new InvalidRequestException('网络延迟');

        TeethCompany::isExists($id);

        $data['company'] = TeethCompany::oneCompany($id);
        $data['ad'] = Ad::getAd($id, 'company_index', 4);
        $data['is_admin'] = WechatUser::isAdmin($request->user('api')->id, $id);
        $data['is_salesman'] = WechatUser::isSale($request->user('api'), $id);
        //没有看牙卡，就创建
        DentalCard::cardExits($request->user('api')->id, $request->all());
        return $this->reponseJson(ErrorCode::SUCCESS, $data);
    }

    public function ownCompanyQrcode(Request $request)
    {
        $user = $request->user('api');
        $teethCompany = new TeethCompanyService();
        return $this->reponseJson(ErrorCode::SUCCESS, $teethCompany->ownCompany($user));
    }
}
