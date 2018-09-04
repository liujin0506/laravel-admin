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
use App\Library\Core\Common;
use App\Library\Jd\Jd;
use GuzzleHttp\Client;
use function GuzzleHttp\Psr7\parse_query;
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

    public function send_wechat(Request $request)
    {
        return $request->all();
    }
}