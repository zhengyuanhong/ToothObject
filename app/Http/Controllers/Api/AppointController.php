<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Resources\AppointRecordResource;
use App\Models\AppointRecord;
use App\Models\Customer;
use App\Models\TeethCompany;
use App\Services\AppointService;
use App\Utils\ErrorCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AppointController extends Controller
{
    protected $appointService = null;

    public function __construct(AppointService $appointService)
    {
        $this->appointService = $appointService;
    }

    public function updateAppoint(Request $request, TeethCompany $teethCompany)
    {
        $record_id = $request->get('record_id');
        if (empty($record_id)) throw new InvalidRequestException('网络延迟');

        $type = $request->get('type', 'cancel');
        //判断权限
        $this->appointService->can($type, $request->user('api'), $teethCompany->id);

        $this->appointService->update($record_id, $type);
        return $this->reponseJson(ErrorCode::SUCCESS);
    }

    //标记已经到院
    public function arrived(Request $request)
    {
        $record_id = $request->get('record_id');
        if (empty($record_id)) throw new InvalidRequestException('网络延迟');

        //取消预约
        //还原洗牙预约数量
        $this->appointService->returnRecord($record_id);
        return $this->reponseJson(ErrorCode::SUCCESS);
    }

    public function appointFilm(Request $request)
    {
        $input = $request->all();
        $validate = Validator::make($input, [
            'name' => 'required',
            'phone' => 'required',
            'time' => 'required',
            'company_id' => 'required',
            'sale_user_id' => '',
        ]);
        if ($validate->fails()) {
            Log::info('App\Controller\Api\AppointController@appointFilm' . __LINE__, ErrorCode::NO_PARAM);
            throw new InvalidRequestException('请填写完整的信息');
        }

        DB::transaction(function () use ($request, $input) {
            $this->appointService->appoint($input, auth('api')->user(), 'film');
            //新建客户
            Customer::create($request->user('api')->id, $input);
        });
        return $this->reponseJson(ErrorCode::SUCCESS);
    }

    public function records(Request $request)
    {
        $records = AppointRecord::query()
            ->companyAndUser($request->get('company_id'), auth('api')->user()->id)
            ->where('appoint_status', '<>', AppointRecord::STATUS['CANCEL'])
            ->orderBy('created_at','DESC')
            ->paginate(20);

        return AppointRecordResource::collection($records);
    }

    public function customerRecord(Request $request, TeethCompany $teethCompany)
    {
        TeethCompany::isAdminOrSale($request->user('api'), $teethCompany->id);

        $res = AppointRecord::query()->where('company_id', $teethCompany->id)
            ->where('sale_user_id', $request->user('api')->id)
            ->where('appoint_status', '<>', AppointRecord::STATUS['CANCEL'])
            ->orderBy('created_at','DESC')
            ->paginate(10);
        return AppointRecordResource::collection($res);
    }

    public function queryPrice(Request $request)
    {
        $input = $request->all();
        $validate = Validator::make($input, [
            'obj_name' => 'required',
            'company_id' => 'required',
            'sale_user_id' => '',
        ]);
        if ($validate->fails()) {
            throw new InvalidRequestException('请填写完整的信息');
        }

        $this->appointService->queryPrice($input, $request->user('api'));
        return $this->reponseJson(ErrorCode::SUCCESS);
    }
}
