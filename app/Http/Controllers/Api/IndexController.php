<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Resources\ActResource;
use App\Models\Activity;
use App\Models\Ad;
use App\Models\AppointRecord;
use App\Models\DentalCard;
use App\Models\TeethCompany;
use App\Models\WechatUser;
use App\Services\TeethCompanyService;
use App\Services\WechatUserService;
use App\Utils\ErrorCode;
use App\Utils\GeoHash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

    public function nearAct(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'lat' => 'required',
            'lon' => 'required'
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException('缺少参数');
        }

        $hash = $this->returnSearchHash($input['lat'], $input['lon'], 1);
        $res = Activity::query()
            ->where('status', Activity::STATUS['starting'])
            ->where('geo_code', 'like', $hash . '%')
            ->paginate(20);
        return ActResource::collection($res);
    }

    public function returnSearchHash($lat, $lon, $length)
    {
        $hash = (new GeoHash())->encode($lat, $lon);
        return substr($hash, 0, $length);
    }
}
