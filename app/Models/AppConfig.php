<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppConfig extends Model
{
    use HasFactory;

    protected $table = 'config';

    protected $fillable = ['key', 'value', 'domain', 'name'];

    static public function set($key, $value, $domain = null, $desc = '')
    {
        $data = [
            'key' => $key,
            'value' => $value
        ];
        if (!empty($desc)) {
            $data['name'] = $desc;
        }

        if (!empty($domain)) {
            $data['domain'] = $domain;
        }

        self::query()->create($data);
    }

    static public function get($key, $domain)
    {
        $query =  self::query();
        if(empty($domain)){
            $query->whereNull('domain');
        }
        $query->where('domain', $domain)
            ->where('key', $key)
            ->select(['key', 'value']);
    }

    static public function fix($key, $value, $domain)
    {
        self::query()->where('domain', $domain)
            ->where('key', $key)
            ->update([
                'value' => $value
            ]);
    }

}
