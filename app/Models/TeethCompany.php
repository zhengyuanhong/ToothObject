<?php

namespace App\Models;

use App\Admin\Controllers\WechatUserController;
use App\Exceptions\InvalidRequestException;
use http\Exception\InvalidArgumentException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeethCompany extends Model
{
    use HasFactory;

    const STATUS = [
        'wait' => 1,
        'fail' => 0,
        'success' => 2
    ];

    const STATUS_TEXT = [
        0 => '失败',
        1 => '审核',
        2 => '成功'
    ];

    const MAX_NUMBER = 20;

    protected $table = 'teeth_company';

    protected $fillable = ['phone', 'slogan', 'user_id', 'status', 'address', 'card_name', 'company_name', 'lat', 'lon', 'index_head_image', 'company_id'];

    static public function companyInfo($company_id)
    {
        return self::query()->where('status', self::STATUS['success'])->find($company_id);
    }

    static public function oneCompany($id)
    {
        $res = self::companyInfo($id);
        return [
            'id' => $res->id,
            'indicatorDots' => true,
            'index_head_image' => $res->index_head_image,
            'phone' => $res->phone,
            'slogan' => $res->slogan,
            'logo' => $res->logo,
            'user_id' => $res->user_id,
            'status' => $res->status,
            'address' => $res->address,
            'card_name' => $res->card_name,
            'name' => $res->company_name,
            'latitude' => $res->lat,
            'longitude' => $res->lon,
            'markers' => [
                [
                    'id' => $res->id,
                    'latitude' => $res->lat,
                    'longitude' => $res->lon,
                    'title' => $res->company_name
                ]
            ]
        ];
    }

    function getLatAttribute($val)
    {
        return floatval($val);
    }

    function getLonAttribute($val)
    {
        return floatval($val);
    }

    static function createQrCode($company_id, $user_id)
    {
        $app = app('easyWechat');
        $response = $app->app_code->getUnlimit('company_id=' . $company_id . '&salesman_id=' . $user_id, [
            'page' => 'pages/company/company',
            'width' => 600,
        ]);
        $filename = null;
        if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
            $filename = $response->saveAs(storage_path('public\qrcode'), $user_id . ':' . time() . '.png');
        }
        return $filename;

    }

    public function customer()
    {
        return $this->hasMany(Customer::class, 'company_id', 'id');
    }

    static function add(TeethCompany $company, $user_id)
    {
        if (SalesMan::query()->where('company_id', $company->id)->where('user_id', $user_id)->exists()) {
            throw new InvalidRequestException('你已加入团队');
        }

        if ($company->salesman()->count() > self::MAX_NUMBER) {
            throw new InvalidRequestException('团队人数已上限');
        }
        $company->salesman()->attach($user_id);
    }

    public function salesman()
    {
        return $this->belongsToMany(WechatUser::class, 'salesman', 'company_id', 'user_id')->withPivot('id');
    }

    public function cards()
    {
        return $this->hasMany(DentalCard::class, 'company_id', 'id');
    }

    static public function isExists($company_id)
    {
        if (empty($company = self::query()->find($company_id))) {
            throw new InvalidRequestException('该口腔医院不存在');
        }
        return $company;
    }

    static public function isAdminOrSale($user, $teethCompany_id)
    {
        if (!(WechatUser::isSale($user, $teethCompany_id) || WechatUser::isAdmin($user->id, $teethCompany_id))) {
            throw new InvalidRequestException('你的权限不够');
        }
    }
}
