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
use Illuminate\Http\Request;

class LinkController extends Controller
{
    public function trans(Request $request)
    {
        $params = $request->all();
        $link = data_get($params, 'link', '');
        return [
            'old' => $link,
            'link' => $link,
            'url' => $link
        ];
    }

    public function send_wechat(Request $request)
    {
        return $request->all();
    }
}