<?php

/**
 * 后台相关的路由
 */
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'system', 'namespace' => 'System'], function () {
    Route::post('login', 'LoginController@login');
    Route::post('register', 'RegisterController@register');
});

Route::group(['prefix' => 'system', 'namespace' => 'System', 'middleware' => 'auth.jwt'], function () {
    Route::post('logout', 'LoginController@logout');
    Route::get('user/detail', 'UserController@detail');
    Route::post('refresh', 'UsersController@refresh');

    /** 权限集 */
    Route::resource('aca', 'AcaController');
    /** 角色 */
    Route::resource('role', 'RoleController');
    Route::get('role/{id}/aca', 'RoleController@get_aca');
    Route::post('role/{id}/aca', 'RoleController@set_aca');
    /** 用户 */
    Route::resource('user', 'UserController');
    Route::post('user/{id}/change_status', 'UserController@change_status');
    Route::post('user/{id}/change_password', 'UserController@change_password');
    Route::get('user/{id}/role', 'UserController@get_roles');
    Route::post('user/{id}/role', 'UserController@set_roles');
});

Route::group(['prefix' => 'cms', 'namespace' => 'Cms', 'middleware' => 'auth.jwt'], function () {
    /** 内容 */
    Route::resource('content', 'ContentController');
});
