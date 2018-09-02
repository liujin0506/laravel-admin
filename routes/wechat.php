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
    Route::get('lists', 'GoodsController@lists');
    Route::get('detail/{id}', 'GoodsController@detail');
});