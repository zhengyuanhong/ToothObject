<?php

use App\Http\Controllers\Api\AdController;
use App\Http\Controllers\Api\AppointController;
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

Route::prefix('v1')->namespace('api')->group(function(){
   Route::put('login',[UserController::class,'register']);
   Route::get('un-authenticate',[UserController::class,'unAuth'])->name('login');
});


Route::prefix('v1')->namespace('api')->middleware('auth:sanctum')->group(function(){
    Route::get('/teeth-data',[CleanTeethController::class,'getCleanTeethData']);
    Route::post('/form-data',[CleanTeethController::class,'updateCleanTeethData']);
    Route::get('/one-appoint',[CleanTeethController::class,'getUserRecord']);
    Route::get('/ad-article',[AdController::class,'oneAdArticle']);
    Route::get('/ad',[AdController::class,'oneAd']);

    Route::get('/cancel-appoint',[AppointController::class,'updateAppoint']);
    Route::post('/appoint-film',[AppointController::class,'appointFilm']);
    Route::get('/records',[AppointController::class,'records']);

    Route::get('/user-info',[UserController::class,'userInfo']);
    Route::post('/draw-card',[UserController::class,'drawCard']);
    Route::get('/customer',[UserController::class,'customer']);

    Route::get('/search',[CustomerController::class,'search']);

    Route::get('/index',[IndexController::class,'index']);
});
