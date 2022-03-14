<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Resources\AppointRecordResource;
use App\Http\Resources\CustomerResource;
use App\Models\AppointRecord;
use App\Models\Customer;
use App\Models\TeethCompany;
use App\Models\WechatUser;
use App\Utils\ErrorCode;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function search(Request $request)
    {
        $key_world = $request->get('key_world', '');
        if (empty($key_world)) throw new InvalidRequestException('请输入关键词');
        return $this->reponseJson(ErrorCode::SUCCESS, Customer::search($request->user('api'), $key_world));
    }

    public function customer(Request $request)
    {
        $company_id = $request->get('company_id');
        $user = $request->user('api');
        TeethCompany::isExists($company_id);
        TeethCompany::isAdminOrSale($user, $company_id);

        $res = Customer::query()
            ->with('salesman')
            ->where('company_id', $company_id)
            ->where('sale_user_id', $user->id)
            ->orderBy('created_at', 'DESC')->paginate(20);
        return CustomerResource::collection($res);
    }
}
