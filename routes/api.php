<?php

use App\Http\Controllers\Api\AdController;
use App\Http\Controllers\Api\AppointController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\IndexController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CleanTeethController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->namespace('api')->group(function () {
    Route::put('register', [UserController::class, 'register']);
    Route::get('un-authenticate', [UserController::class, 'unAuth'])->name('login');
    Route::get('/company', [CompanyController::class, 'company']);
});


Route::prefix('v1')->namespace('api')->middleware('auth:sanctum')->group(function () {
    Route::get('/teeth-data', [CleanTeethController::class, 'getCleanTeethData']);
    Route::post('/form-data', [CleanTeethController::class, 'updateCleanTeethData']);
    Route::get('/one-appoint/{teethCompany}', [CleanTeethController::class, 'getUserRecord']);
    Route::get('/ad-article', [AdController::class, 'oneAdArticle']);
    Route::get('/ad', [AdController::class, 'oneAd']);
    Route::get('/teeth-detail', [AdController::class, 'teethObjDetail']);

    Route::get('/update-appoint/{teethCompany}', [AppointController::class, 'updateAppoint']);
    Route::post('/appoint-film', [AppointController::class, 'appointFilm']);
    Route::get('/records', [AppointController::class, 'records']);
    Route::get('/customer-record/{teethCompany}', [AppointController::class, 'customerRecord']);
    Route::get('/query-price', [AppointController::class, 'queryPrice']);

    Route::get('/card-info/{teethCompany}', [UserController::class, 'cardInfo']);
    Route::get('/user-info', [UserController::class, 'userInfo']);
    Route::post('/draw-card', [UserController::class, 'drawCard']);
    Route::post('/phone', [UserController::class, 'getPhoneNumber']);
    Route::get('/login', [UserController::class, 'wechatUserLogin']);
    Route::get('/other-user-info', [UserController::class, 'otherUserInfo']);
    Route::post('/update-user-info', [UserController::class, 'updateUserInfo']);

    Route::get('/customer', [CustomerController::class, 'customer']);
    Route::get('/search', [CustomerController::class, 'search']);
    Route::get('/index', [IndexController::class, 'index']);
    Route::get('/own-company-qr-code', [IndexController::class, 'ownCompanyQrcode']);

    Route::get('/get-sale/{teethCompany}', [CompanyController::class, 'getSale']);
    Route::get('/add-sale', [CompanyController::class, 'addSale']);
    Route::get('/del-sale/{teethCompany}', [CompanyController::class, 'deleteSale']);
    Route::get('/invite-code/{teethCompany}', [CompanyController::class, 'inviteCode']);
    Route::get('/qr-code/{teethCompany}', [CompanyController::class, 'getQrCode']);
    Route::post('/upload-img', [CompanyController::class, 'uploadImg']);
    Route::post('/settle', [CompanyController::class, 'settle']);

});
