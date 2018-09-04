<?php
/**
 * Description  CmsTop MediaCloud
 *
 * @Author      liujing <liujing@cmstop.com>
 * @DateTime    2018/9/3 11:43
 * @CopyRight   Beijing CmsTop Technology Co.,Ltd.
 */

namespace App\Http\Controllers\Wechat\Link;

use App\Http\Controllers\Controller;
use App\Http\Services\GoodsService;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    public function trans(Request $request, GoodsService $service)
    {
        $params = $request->all();
        $user = auth('wap')->user();
        $user['openid'] = $request->get('openid');
        return $service->transLink($params, $user);
    }

    public function send_wechat(Request $request, GoodsService $service)
    {
        $params = $request->all();
        $openid = $request->attributes->get('openid');
        return $service->sendWechat($openid, $params);
    }
}