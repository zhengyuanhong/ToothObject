<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\SalesmanResource;
use App\Models\SalesMan;
use App\Models\TeethCompany;
use App\Models\WechatUser;
use App\Services\WechatUserService;
use App\Utils\ErrorCode;
use App\Utils\GeoHash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    public function company(Request $request)
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
        $res = TeethCompany::query()
            ->where('status', TeethCompany::STATUS['success'])
            ->where('geo_code', 'like', $hash . '%')
            ->paginate(20);
        return CompanyResource::collection($res);
    }

    public function returnSearchHash($lat, $lon, $length)
    {
        $hash = (new GeoHash())->encode($lat, $lon);
        return substr($hash, 0, $length);
    }

    public function addSale(Request $request, WechatUserService $wechatUserService)
    {
        $validator = Validator::make($request->all(), [
            'invite_code' => 'required',
            'name' => 'required',
            'avatar' => 'required',
            'gender' => 'required',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException('邀请码错误');
        }

        $key = 'add-sale-' . $request->get('invite_code');
        //检查是否过期
        $company_id = Cache::get($key);
        if (empty($company_id)) {
            throw new InvalidRequestException('邀请码过期');
        }

        $company = TeethCompany::isExists($company_id);

        $user_id = $request->user('api')->id;
        DB::transaction(function () use ($request, $company, $user_id, $wechatUserService) {
            TeethCompany::add($company, $user_id);

            $wechatUserService->saveUser($user_id, $request->all());
        });
        //如果存在key 则删除key
        if (Cache::has($key)) Cache::forget($key);

        return $this->reponseJson(ErrorCode::SUCCESS);
    }

    public function deleteSale(Request $request, TeethCompany $teethCompany)
    {
        TeethCompany::isExists($teethCompany->id);
        $this->_authorize($teethCompany, $request->user('api'));

        $teethCompany->salesman()->detach($request->get('user_id'));
        return $this->reponseJson(ErrorCode::SUCCESS);
    }

    public function getSale(TeethCompany $teethCompany)
    {
        TeethCompany::isExists($teethCompany->id);

        $res = $teethCompany->salesman()->paginate(3);
        return SalesmanResource::collection($res);
    }

    public function inviteCode(Request $request, TeethCompany $teethCompany)
    {
        TeethCompany::isExists($teethCompany->id);
        $this->_authorize($teethCompany, $request->user('api'));

        $data = [
            'invite_code' => uniqid(),
            'admin_user_id' => $request->user('api')->id
        ];

        $key = 'add-sale-' . $data['invite_code'];

        Cache::put($key, $teethCompany->id, now()->addMinutes(10));
        return $this->reponseJson(ErrorCode::SUCCESS, $data);
    }

    public function getQrCode(Request $request, TeethCompany $teethCompany)
    {
        if (!WechatUser::isSale($request->user('api'), $teethCompany->id)) {
            throw new InvalidRequestException('你不是本机构的业务员');
        }

        $saleMan = SalesMan::query()
            ->where('company_id', $teethCompany->id)
            ->where('user_id', $request->user('api')->id)
            ->first();

        $qr_code = null;
        $data = [];
        if (empty($saleMan->qr_code)) {
            $qr_code = TeethCompany::createQrCode($teethCompany->id, $request->user('api')->id);
            Log::info('生成二维码失败' . __LINE__);
            if (!empty($qr_code)) {
                $saleMan->qr_code = $qr_code;
                $saleMan->save();
            }
        } else {
            $qr_code = $saleMan->qr_code;
        }
        $data['qr_code'] = $qr_code;
        return $this->reponseJson(ErrorCode::SUCCESS, $data);
    }

    public function _authorize(TeethCompany $teethCompany, WechatUser $user)
    {
        if ($teethCompany->user_id != $user->id) throw  new InvalidRequestException('权限不够');
    }
}