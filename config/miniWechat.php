<?php
/**
 * Created by PhpStorm.
 * User: zheng
 * Date: 2022/1/10
 * Time: 20:16
 */
return [
    'mini_wechat'=>[
        'appid'=>env('APPID','appid'),
        'secret'=>env('SECRET','secret')
    ],
    'message'=>[
        'appoint'=>'QEn7cF3QOpjGDDyO8AZCXFevxERJhWMH7_aO8MUr6Cs',
        'schedule'=>'UKX3ChBpwcuNsq_NyFnnFVFPEHJ2-8p2NaB4m6oZjTs'
    ],
    'company'=>[
        'phone' => env('PHONE'),
        'user_id' => env('USER_ID',0),
        'slogan' => env('SLOGAN'),
        'address' => env('ADDRESS'),
        'card_name' => env('CARD_NAME'),
        'company_name' => env('COMPANY_NAME'),
        'lat' => env('LAT'),
        'lon' => env('LON'),
    ]
];
