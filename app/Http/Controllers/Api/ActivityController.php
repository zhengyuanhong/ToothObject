<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Resources\ActResource;
use App\Models\Activity;
use App\Models\TeethCompany;
use App\Models\WechatUser;
use App\Utils\ErrorCode;
use App\Utils\GeoHash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ActivityController extends Controller
{
    public function activity(Request $request)
    {
        $activity_id = $request->get('activity_id');
        $company_id = $request->get('company_id');
        if (empty($activity_id) || empty($company_id)) {
            throw new InvalidRequestException('缺少参数');
        }

        $res = Activity::query()->find($activity_id);
        $data['activity_exists'] = true;
        if (empty($res)) {
            $data['activity_exists'] = false;
            return $this->reponseJson(ErrorCode::SUCCESS,$data['activity_exists']);
        }

        if ($res->company_id != $company_id) {
            throw new InvalidRequestException('活动不属于该机构');
        }
        $data['activity'] = $res->toArray();

        return $this->reponseJson(ErrorCode::SUCCESS, $data);
    }

    public function delAct(Request $request)
    {
        /** @var WechatUser $user */
        $user = $request->user('api');
        $activity_id = $request->get('activity_id');
        $company_id = $request->get('company_id');

        $company = TeethCompany::isExists($company_id);
        TeethCompany::isAdminOrSale($user, $company->id);

        if (empty($activity_id) || empty($company_id)) {
            throw new InvalidRequestException('缺少参数');
        }

        $res = Activity::query()->find($activity_id);
        if (empty($res)) {
            throw new InvalidRequestException('活动不存在');
        }

        if ($res->company_id != $company_id) {
            throw new InvalidRequestException('活动不属于该机构');
        }

        $res->delete();

        return $this->reponseJson(ErrorCode::SUCCESS);
    }

    public function activities(Request $request)
    {
        /** @var WechatUser $user */
        $user = $request->user('api');
        $company_id = $request->get('company_id');
        $company = TeethCompany::isExists($company_id);
        TeethCompany::isAdminOrSale($user, $company->id);

        $activity = $user->activity()
            ->where('company_id', $company_id)
            ->orderBy('created_at', 'DESC')
            ->paginate(10);
        return ActResource::collection($activity);

    }

    public function createAct(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'images' => 'required',
            'name' => 'required',
            'lat' => 'required',
            'lon' => 'required',
            'content' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'company_id' => 'required',
        ]);
        if ($validator->fails()) {
            throw new InvalidRequestException('请填写完整信息');
        }
        $input['user_id'] = $request->user('api')->id;
        $input['status'] = Activity::STATUS['starting'];
        $input['geo_code'] = (new GeoHash())->encode($input['lat'], $input['lon']);
        Activity::query()->create($input);

        return $this->reponseJson(ErrorCode::SUCCESS);
    }

    public function updateAct(Request $request)
    {

        $input = $request->all();
        $validator = Validator::make($input, [
            'images' => 'required',
            'name' => 'required',
            'lat' => 'required',
            'lon' => 'required',
            'content' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'company_id' => 'required',
        ]);
        if ($validator->fails()) {
            throw new InvalidRequestException('请填写完整信息');
        }
        $input['status'] = Activity::STATUS['starting'];
        $input['geo_code'] = (new GeoHash())->encode($input['lat'], $input['lon']);

        $res = Activity::query()->find($input['activity_id']);
        $res->update($input);

        return $this->reponseJson(ErrorCode::SUCCESS);
    }
}
