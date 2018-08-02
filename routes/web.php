<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//这句接管路由
$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {
    $api->post('login', 'App\Http\Api\Auth\LoginController@login');
    $api->post('register', 'App\Http\Api\Auth\RegisterController@register');
    $api->group(['middleware' => 'api.auth'], function ($api) {
        $api->get('user', 'App\Http\Api\UsersController@index');
    });
});
