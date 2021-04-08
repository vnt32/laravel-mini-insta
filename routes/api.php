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

Route::get('', 'Api\User\UserController@index');

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

    Route::get('/posts/{username}', 'Api\Posts\UserPostsController@getUserPosts');
    Route::post('/post', 'Api\Posts\UserPostsController@addPost');
    Route::get('/post/{id}', 'Api\Posts\UserPostsController@getPostById');
    Route::delete('/post/{id}', 'Api\Posts\UserPostsController@removePost');

    Route::get('/user/{username}', 'Api\User\UserController@getUserByUsername');

    Route::get('followers/{username}', 'Api\User\UserController@followers');
    Route::get('followed/{username}', 'Api\User\UserController@followed');
    Route::get('followers', 'Api\User\UserController@followers');
    Route::get('followed', 'Api\User\UserController@followed');

    Route::post('follow/{id}', 'Api\User\UserController@follow');
    Route::post('unfollow/{id}', 'Api\User\UserController@unfollow');
});

