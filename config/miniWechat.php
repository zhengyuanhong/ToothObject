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
        'appoint'=>'eVhhpoEWrJGPecxMxcY4Z0uNuaUvpN6ZusDYvAtU7ow',
        'schedule'=>'57p3KiZA6zK4rF7sQ8_rOCd3D6gpmDGIALGQcsLMzYQ'
    ],
    'company'=>[
        'phone' => env('PHONE'),
        'user_id' => env('USER_ID',0),
        'slogan' => env('SLOGAN'),
        'index_head_image' => env('INDEX_HEAD_IMAGE'),
        'address' => env('ADDRESS'),
        'card_name' => env('CARD_NAME'),
        'company_name' => env('COMPANY_NAME'),
        'lat' => env('LAT'),
        'lon' => env('LON'),
    ]
];
