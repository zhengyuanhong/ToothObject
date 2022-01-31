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
        return $this->hasOne(DentalCard::class, 'user_id', 'id');
    }

    public function customer()
    {
        return $this->hasMany(Customer::class, 'user_id', 'id');
    }

    public function company(){
        return $this->hasOne(TeethCompany::class,'user_id','id');
    }

    public function message(){
        return $this->hasMany(Message::class,'user_id','id');
    }
}

