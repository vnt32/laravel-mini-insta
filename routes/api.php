<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "Api" middleware group. Enjoy building your API!
|
*/
//
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('country', 'Api\Country\CountryController@country');
Route::get('country/{id}', 'Api\Country\CountryController@countryById');
Route::post('login', 'Api\Auth\AuthController@login');
Route::post('register', 'Api\Auth\AuthController@register');

Route::group(['middleware' => ['jwt.verify']], function (){
    Route::post('country', 'Api\Country\CountryController@addCountry');
    Route::put('country/{id}', 'Api\Country\CountryController@editCountry');
    Route::delete('country/{id}', 'Api\Country\CountryController@deleteCountry');

    Route::get('refresh', 'Api\Auth\AuthController@refresh');
    Route::get('me', 'Api\Auth\AuthController@getMe');
    Route::post('me/update', 'Api\Auth\AuthController@editMe');
});

