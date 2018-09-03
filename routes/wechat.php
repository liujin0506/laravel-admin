<?php

use Illuminate\Support\Facades\Route;

Route::any('serve', 'Common\ServeController@serve');

Route::group(['prefix' => 'member', 'namespace' => 'Member'], function () {
    Route::get('auth', 'AuthController@auth');
    Route::get('redirect', 'AuthController@redirect');
});

Route::group(['prefix' => 'member', 'namespace' => 'Member', 'middleware' => 'auth.wap'], function () {
    Route::get('info', 'AuthController@info');
    Route::post('bind', 'AuthController@bind');
});

Route::group(['prefix' => 'goods',  'namespace' => 'Goods'], function () {
    Route::get('category', 'CategoryController@index');
    Route::get('lists', 'GoodsController@lists');
    Route::get('detail/{id}', 'GoodsController@detail');
});

Route::group(['prefix' => 'link',  'namespace' => 'Link'], function () {
    Route::post('trans', "LinkController@trans");
    Route::post('send_wechat', "LinkController@send_wechat");
});