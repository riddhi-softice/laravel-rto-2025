<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\BlogApiController;
use App\Http\Controllers\Api\VideoController;
use App\Http\Controllers\Api\UrlController;

Route::group(['middleware' => ['throttle:1000,1'], 'as' => 'api.'], function () {
    
    Route::post('user_count', [ApiController::class, 'user_count']);
    Route::post('get_city_details', [ApiController::class, 'get_city_details']);
    Route::post('get_city_list', [ApiController::class, 'get_city_list']);
    Route::post('get_price_city_list', [ApiController::class, 'get_price_city_list']);
    Route::post('get_article', [ApiController::class, 'get_article']);
    Route::post('get_today_prices', [ApiController::class, 'get_today_prices']);
    Route::post('get_today_prices_city_wise', [ApiController::class, 'get_today_prices_city_wise']);
    Route::post('get_common_setting', [ApiController::class, 'get_common_setting']);
    Route::post('get_privacy', [ApiController::class, 'get_privacy']);
   
    Route::middleware('custome.api')->group( function () {
        Route::post('/vehicle-info', [VehicleController::class, 'getVehicleInfo']);
        Route::post('/challan-info', [VehicleController::class, 'getChallanInfo']);
        Route::post('/fetch-dl-info', [VehicleController::class, 'fetchDLInfo']);
    });
    Route::post('/list_ipo', [ApiController::class, 'getIPOList']);
    
    Route::post('home_blog_list', [BlogApiController::class, 'home_blog_list']);
    Route::post('blog_category_list', [BlogApiController::class, 'blog_category_list']);
    Route::post('blog_list', [BlogApiController::class, 'blog_list']);
    Route::post('blog_details', [BlogApiController::class, 'blog_details']);

    Route::post('get_video', [VideoController::class, 'get_video']);
    Route::post('get_home_video', [VideoController::class, 'get_home_video']);
      
    Route::get('trackurl',[UrlController::class,'trackurl']);
    Route::post('url_list',[UrlController::class,'url_list']);
});
Route::get('/test', [ApiController::class, 'fetchAndStore']);
Route::post('/noti_test', [ApiController::class, 'noti_test']);
Route::get('/store_city', [ApiController::class, 'store_city']);