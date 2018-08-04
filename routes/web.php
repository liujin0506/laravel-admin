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
        $api->get('user/detail', AdminName . 'System\UserController@detail');
        $api->post('refresh', AdminName . 'System\UsersController@refresh');

        /** 权限集 */
        $api->resource('aca', AdminName . 'System\AcaController');
        /** 角色 */
        $api->resource('role', AdminName . 'System\RoleController');
        $api->get('role/{id}/aca', AdminName . 'System\RoleController@get_aca');
        $api->post('role/{id}/aca', AdminName . 'System\RoleController@set_aca');
        /** 用户 */
        $api->resource('user', AdminName . 'System\UserController');
        $api->post('user/{id}/change_status', AdminName . 'System\UserController@change_status');
        $api->post('user/{id}/change_password', AdminName . 'System\UserController@change_password');
        $api->get('user/{id}/role', AdminName . 'System\UserController@get_roles');
        $api->post('user/{id}/role', AdminName . 'System\UserController@set_roles');
    });
});
