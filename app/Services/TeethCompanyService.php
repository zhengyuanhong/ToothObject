<?php
/**
 * Created by PhpStorm.
 * User: zheng
 * Date: 2022/3/7
 * Time: 12:18
 */

namespace App\Services;

use App\Exceptions\InvalidRequestException;
use App\Models\TeethCompany;
use App\Utils\GeoHash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeethCompanyService
{
    public function create(Request $request, $data)
    {

        $user = $request->user('api');
        $data['user_id'] = $user->id;
        $data['status'] = TeethCompany::STATUS['wait'];
        $data['geo_code'] = (new GeoHash())->encode($data['lat'], $data['lon']);

        DB::transaction(function () use ($data, $user) {
            /** @var TeethCompany $company */
            $company = TeethCompany::query()->create($data);

            $user->phone = $data['phone'];
            $user->save();

            TeethCompany::addAdminToSale($company, $user->id);
        });
    }

    public function ownCompany($user)
    {

        $res = TeethCompany::query()->where('user_id', $user->id)->first();


        $data['status'] = TeethCompany::STATUS['wait'];
        $data['company'] = false;
        //不存在
        if (empty($res)) {
            return $data;
        }
        //审核中
        if ($res->status == TeethCompany::STATUS['wait']) {
            $data['company'] = true;
            return $data;
        }
        //是否已经有二维码
        if (!empty($res->qr_code)) {
            $data['qr_code'] = $res->qr_code;
            $data['company'] = true;
            $data['status'] = TeethCompany::STATUS['success'];
            return $data;
        }

        if (!empty($res)) {
            $qr_code = TeethCompany::createQrCode('pages/company/company', [
                'company_id' => $res->id
            ]);
            $data['qr_code'] = env('APP_URL') . $qr_code;
            $data['company'] = true;
            $data['status'] = TeethCompany::STATUS['success'];
            $res->qr_code = $data['qr_code'];
            $res->save();
        }
        return $data;
    }
}
