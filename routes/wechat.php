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

Route::get('test', function () {
    // 主图 350 * 350
    // 生成图 350 * 550
    $img = Image::canvas(350, 500, '#fff');
    $thumb = 'http://img14.360buyimg.com/n1/jfs/t18997/133/1423890958/426440/d015b4b9/5aca0554N15e234cf.jpg';
    $thumb_tmp = "/tmp/" . md5(uniqid($thumb)) . 'png';
    file_put_contents($thumb_tmp, file_get_contents($thumb));
    $img->insert($thumb_tmp);

    $qr_tmp = '/tmp/' . md5(uniqid());
    QrCode::format('png')->size(150)->margin(1)->generate("https://www.baidu.com/", $qr_tmp);
    $img->insert($qr_tmp, 'bottom-right');

    $img->text('aaaaa', 10, 350, function ($font) {
        $font->file(storage_path('font') . '/apple.ttf');
        $font->size(40);
    });

    $new_path = "/tmp/" . md5(uniqid($thumb_tmp)) . 'png';
    $img->save($new_path);
    return \Illuminate\Http\Response::create(file_get_contents($new_path), 200, [
        'Content-Type' => 'image/png'
    ]);
});

Route::get('poster', function (\Illuminate\Http\Request $request) {
     $params = $request->all();
     return view('wechat/poster', [
        'thumb' => $params['thumb'],
        'title' => $params['title'],
        'real_price' => $params['real_price'],
        'discount' => $params['discount'],
        'new_price' => $params['new_price'],
        'url' => $params['url']
    ]);
});

Route::get('test', function () {
    $client = new \GuzzleHttp\Client();
    $data = $client->post('http://127.0.0.1:7777/html2Image', [
        'header' => [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ],
        'form_params' => [
            'url' => 'http://wx.jd.risay.cn/poster',
            'type' => 'base64',
            'width' => 350,
            'height' => 500
        ]
    ]);
    $data = $data->getBody()->getContents();
    file_put_contents(storage_path('app') . '/poster.png', base64_decode($data));
});