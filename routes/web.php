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

const NAME = 'App\Http\Controllers\Admin\\';

$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {
    $api->group(['prefix' => 'system'], function ($api) {
        $api->post('login', NAME . 'System\LoginController@login');
        $api->post('register', NAME . 'System\RegisterController@register');
    });


    $api->group(['middleware' => 'dingo_auth', 'prefix' => 'system'], function ($api) {
        $api->post('logout', NAME . 'System\LoginController@logout');
        $api->get('user/detail', NAME . 'System\UserController@detail');
        $api->post('refresh', NAME . 'System\UsersController@refresh');

        /** 权限集 */
        $api->resource('aca', NAME . 'System\AcaController');
        /** 角色 */
        $api->resource('role', NAME . 'System\RoleController');
        $api->get('role/{id}/aca', NAME . 'System\RoleController@get_aca');
        $api->post('role/{id}/aca', NAME . 'System\RoleController@set_aca');
        /** 用户 */
        $api->resource('user', NAME . 'System\UserController');
        $api->post('user/{id}/change_status', NAME . 'System\UserController@change_status');
        $api->post('user/{id}/change_password', NAME . 'System\UserController@change_password');
        $api->get('user/{id}/role', NAME . 'System\UserController@get_roles');
        $api->post('user/{id}/role', NAME . 'System\UserController@set_roles');
    });
});
