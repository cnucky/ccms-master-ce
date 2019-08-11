<?php

use Illuminate\Http\Request;

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

// Route::middleware('auth:api')->get('/currentUser', 'API\\UserController@currentUser')->name('currentUser');

Route::prefix("slave")->group(function () {
    Route::middleware(\App\Http\Middleware\ComputeNodeSlaveAPIAuthenticate::class)->group(function () {
        Route::prefix("computeNodes")->group(function () {
            Route::prefix("{computeNode}")->group(function () {
                Route::post("/computeInstances/{computeInstance}/started", "SlaveAPI\\ComputeInstancePowerEventController@started");
                Route::post("/computeInstances/{computeInstance}/stopped", "SlaveAPI\\ComputeInstancePowerEventController@stopped");

                Route::post("/ping", "SlaveAPI\\PingController")->name("ping");
            });
        });
    });
});

Route::any("/paymentModules/{paymentModule}/notify/{tradeNo?}", "PaymentModule\\NotifyController@notify")->name("paymentModules.notify");
Route::any("/paymentModules/{paymentModule}/payNotify/{tradeNo?}", "PaymentModule\\NotifyController@payNotify")->name("paymentModules.payNotify");
Route::any("/paymentModules/{paymentModule}/refundNotify/{tradeNo?}", "PaymentModule\\NotifyController@refundNotify")->name("paymentModules.refundNotify");