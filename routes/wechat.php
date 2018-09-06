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

Route::group(['prefix' => 'goods',  'namespace' => 'Goods', 'middleware' => 'auth.wap'], function () {
    Route::get('category', 'CategoryController@index');
    Route::get('lists', 'GoodsController@lists');
    Route::get('detail/{id}', 'GoodsController@detail');
    Route::post('spread/{id}', 'GoodsController@spread');
});

Route::group(['prefix' => 'link',  'namespace' => 'Link', 'middleware' => 'auth.wap'], function () {
    Route::post('trans', "LinkController@trans");
    Route::post('send_wechat', "LinkController@send_wechat");
});

Route::get('poster', function (\Illuminate\Http\Request $request) {
     $params = $request->all();
     if (!isset($params['thumb'])) {
         $params = [
             'thumb' => 'http://img14.360buyimg.com/n1/jfs/t21283/39/2598647154/189776/340414ef/5b5ecc7eNb7c53951.jpg',
             'title' => '魔幻厨房 烘焙工具套装 烤箱用品DIY蛋糕模具 饼干披萨蛋挞 做蛋糕西点烘培模具套餐 新手 套装全套套装全套套装全套套装全套',
             'real_price' => '99.00',
             'discount' => '10.00',
             'new_price' => '89.00',
             'url' => 'http://www.baidu.com/'
         ];
     }
     return view('wechat/poster', [
        'thumb' => $params['thumb'],
        'title' => $params['title'],
        'real_price' => $params['real_price'],
        'discount' => $params['discount'],
        'new_price' => $params['new_price'],
        'url' => $params['url']
    ]);
});