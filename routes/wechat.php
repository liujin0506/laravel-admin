<?php

use Illuminate\Support\Facades\Route;

Route::any('serve', 'Common\ServeController@serve');

Route::group(['prefix' => 'member', 'namespace' => 'Member'], function () {
    Route::get('auth', 'AuthController@auth');
    Route::get('redirect', 'AuthController@redirect');
});

Route::group(['prefix' => 'member', 'namespace' => 'Member', 'middleware' => 'auth.wap'], function () {
    Route::get('info', 'AuthController@info');
});