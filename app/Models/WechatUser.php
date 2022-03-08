<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class WechatUser extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    const ROLE = [
        'USER' => 'user',
        'ADMIN' => 'admin'
    ];

    public function appoint_record()
    {
        return $this->hasMany(AppointRecord::class, 'user_id', 'id');
    }

    public function card()
    {
        return $this->hasMany(DentalCard::class, 'user_id', 'id');
    }

    public function customer()
    {
        return $this->hasMany(Customer::class, 'sale_user_id', 'id');
    }

    public function company()
    {
        return $this->hasOne(TeethCompany::class, 'user_id', 'id');
    }

    public function message()
    {
        return $this->hasMany(Message::class, 'user_id', 'id');
    }

    public function ownerCompany()
    {
        return $this->belongsTo(TeethCompany::class, 'company_id', 'id');
    }

    public function companySale()
    {
        return $this->belongsToMany(TeethCompany::class, 'salesman', 'user_id', 'company_id')
            ->withPivot('id');
    }

    static public function isAdmin($user_id, $company_id)
    {
        $company = TeethCompany::query()->find($company_id);
        return $company->user_id == $user_id;
    }

    static public function isSale($user, $company_id)
    {
        return $user->companySale()->where('company_id', $company_id)->exists();
    }
}

