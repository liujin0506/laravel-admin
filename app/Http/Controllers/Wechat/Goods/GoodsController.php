<?php
/**
 * Description  CmsTop MediaCloud
 *
 * @Author      liujing <liujing@cmstop.com>
 * @DateTime    2018/8/30 9:55
 * @CopyRight   Beijing CmsTop Technology Co.,Ltd.
 */

namespace App\Http\Controllers\Wechat\Goods;

use App\Http\Controllers\Controller;
use App\Http\Services\GoodsService;
use Illuminate\Http\Request;

class GoodsController extends Controller
{
    public function lists(Request $request, GoodsService $service)
    {
        return $service->wechatIndex($request->all());
    }

    public function detail($id, GoodsService $service)
    {
        return $service->detail($id);
    }
}