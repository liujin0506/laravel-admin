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

const AdminName = 'App\Http\Controllers\Admin\\';

$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {
    $api->group(['prefix' => 'system'], function ($api) {
        $api->post('login', AdminName . 'System\LoginController@login');
        $api->post('register', AdminName . 'System\RegisterController@register');
    });


    $api->group(['middleware' => 'api.auth', 'prefix' => 'system'], function ($api) {
        $api->post('logout', AdminName . 'System\LoginController@logout');
        $api->get('user', AdminName . 'System\UsersController@index');
        $api->post('refresh', AdminName . 'System\UsersController@refresh');

        $api->resource('aca', AdminName . 'System\AcaController');
    });
});
