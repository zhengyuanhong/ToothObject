<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('home');
    $router->resource('wechat-users', WechatUserController::class);
    $router->resource('ads', AdController::class);
    $router->resource('appoint-records', AppointRecordController::class);
    $router->resource('clean-teeths', CleanTeethController::class);
    $router->resource('customers', CustomerController::class);
    $router->resource('dental-cards', DentalCardController::class);
    $router->resource('messages', MessageController::class);
    $router->resource('teeth-companies', TeethCompanyController::class);

});
