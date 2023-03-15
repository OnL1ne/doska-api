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

Route::group([
    'prefix' => 'auth',
], function ($router) {
    Route::post('login', 'AuthController@login');
    Route::post('registration', 'AuthController@registration');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::get('profile', 'AuthController@profile');
    Route::post('forgot-password', 'AuthController@forgotPassword');
    Route::post('reset-password', 'AuthController@resetPassword');
    Route::post('update-profile', 'AuthController@updateProfile');

    Route::get('get-trainings', 'TrainingsApiController@index');
    Route::post('create-training', 'TrainingsApiController@create');
    Route::delete('delete-training/{id}', 'TrainingsApiController@delete');
    Route::put('update-training/{id}', 'TrainingsApiController@update');
    Route::get('download-training/{id}', 'TrainingsApiController@downloadFile');

    Route::get('get-companies', 'CompaniesApiController@index');
    Route::post('create-company', 'CompaniesApiController@create');
    Route::delete('delete-company/{id}', 'CompaniesApiController@delete');
    Route::put('update-company/{id}', 'CompaniesApiController@update');

    Route::get('get-permissions', 'UsersApiController@getPermissions');
    Route::get('get-events', 'UsersApiController@getEvents');
    Route::get('get-roles', 'UsersApiController@getRoles');
    Route::get('get-users', 'UsersApiController@index');
    Route::post('create-user', 'UsersApiController@create');
    Route::delete('delete-user/{id}', 'UsersApiController@delete');
    Route::put('update-user/{id}', 'UsersApiController@update');
    Route::put('change-user-password/{id}', 'UsersApiController@changePassword');

    Route::get('get-licenses', 'LicensesApiController@index');
    Route::post('create-license', 'LicensesApiController@create');
    Route::delete('delete-license/{id}', 'LicensesApiController@delete');
    Route::put('update-license/{id}', 'LicensesApiController@update');

    //Route::get('email/verify', 'Auth\VerificationController@show')->name('verification.notice');
    Route::get('email/verify/{id}', 'VerificationApiController@verify')->name('verification.verify');
    //Route::get('email/resend', 'Auth\VerificationController@resend')->name('verification.resend');
});
