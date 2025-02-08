<?php
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\CommonSettingController;
use App\Http\Controllers\Admin\AppNotificationController;
use App\Http\Controllers\TwoFactorAuthController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\Admin\UrlConfigController;
use App\Http\Controllers\Admin\UrlBrandController;
use App\Http\Controllers\Admin\ReelController;
use App\Http\Middleware\BlockIpMiddleware;

Route::get('/test', function (Request $request) {
    $clientIp = $request->ip();  
    return $clientIp;  
});

Route::get('cache_clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('route:clear');
    echo "cache cleared..";
});

/* ------  ------ Authentication --------   --------  */
// Route::middleware(['blockIP'])->group(function () {
   
    Route::group(['middleware' => ['admin']], function() {
        Route::get('2fa/setup', [TwoFactorAuthController::class, 'show2faForm'])->name('2fa.form');
        Route::post('2fa/setup', [TwoFactorAuthController::class, 'setup2fa'])->name('2fa.setup');
        Route::get('2fa/verify', [TwoFactorAuthController::class, 'showVerifyForm'])->name('2fa.verifyForm');
        Route::post('2fa/verify', [TwoFactorAuthController::class, 'verify2fa'])->name('2fa.verify');
    });

    /* ------ Authentication --------  */
    Route::get('login', [AuthController::class, 'index'])->name('login');
    Route::post('post-login', [AuthController::class, 'postLogin'])->name('login.post');
    
    // Route::middleware(['2fa','session.timeout','admin'])->group(function () {
    
        Route::get('dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
        Route::get('account_setting', [AuthController::class, 'account_setting'])->name('account_setting');
        Route::post('account_setting_change', [AuthController::class, 'account_setting_change'])->name('post.account_setting');
        Route::get('logout', [AuthController::class, 'logout'])->name('logout');
    
        Route::get('get_setting', [CommonSettingController::class, 'get_setting'])->name('get_setting');
        Route::post('change_setting', [CommonSettingController::class, 'change_setting'])->name('change_setting');
    
        Route::get('privacy_policy', [CommonSettingController::class, 'privacy_policy'])->name('privacy_policy');
        Route::post('change_privacy', [CommonSettingController::class, 'change_privacy'])->name('change_privacy');
    
        Route::resource('url_brand', UrlBrandController::class);
        Route::resource('url_configs', UrlConfigController::class);

        Route::get('url_track', [UrlConfigController::class, 'url_track_index'])->name('url_track.index');
        Route::get('url_track/filter', [UrlConfigController::class, 'url_track_ajax'])->name('url_track.filter');

        // Route::get('/url_track/details/{id}', [UrlConfigController::class, 'track_details'])->name('url_track.details');
        // Route::get('/url_key_track/details/{url_brand_id}/{key}', [UrlConfigController::class, 'key_track_ajax'])->name('url_key_track.details');
        
        Route::resource('app_notification', AppNotificationController::class);
        Route::resource('reels', ReelController::class);
        Route::post('/reels/{id}', [ReelController::class, 'destroy'])->name('reel_destroy');

        Route::resource('tags', TagController::class);
        Route::resource('blogs', BlogController::class);
        Route::post('/blogs/{id}', [BlogController::class, 'destroy'])->name('blog_destroy');
        Route::get('blogs/ajax', [BlogController::class, 'ajax'])->name('blogs.index.ajax');
        Route::post('/update-top-status', [BlogController::class, 'updateTopStatus'])->name('blogs.updateTopStatus');
    // });
// });

Route::get('privacypolicy', function () {
    return view('privacy_policy');
});
Route::get('/', function () {
    return view('landing_page');
});