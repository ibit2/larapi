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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//
Route::post('/login','Native\Account\AuthController@login');
Route::post('/refresh_token','Native\Account\AuthController@refreshToken');
Route::post('/logout','Native\Account\AuthController@logout')->middleware('auth:api');
Route::post('/sms_code','Native\Account\AuthController@sendSmsCode');
Route::post('/password_reset','Native\Account\AuthController@resetPassword');
//
Route::get('/settings/profile','Native\SettingController@profile');
Route::patch('/settings/profile','Native\SettingController@updateProfile');
