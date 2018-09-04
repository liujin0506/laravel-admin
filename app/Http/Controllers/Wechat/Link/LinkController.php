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
use App\Library\Jd\Jd;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    public function trans(Request $request)
    {
        $params = $request->all();
        $link = data_get($params, 'link', '');
        preg_match('/(http|https):\/\/([\w\d\-_]+[\.\w\d\-_]+)[:\d+]?([\/]?[\w\/\.]+?.*)/i', $link, $matches);

        $user = auth('wap')->user();

        $jd = new Jd();
        $data = $jd->request('jingdong.service.promotion.getcode', [
            'promotionType' => '7',
            'materialId' => $matches['0'],
            'unionId' => $user['union_id'],
            'channel' => 'WL',
            'webId' => '0'
        ], 'queryjs_result');
        return [
            'matches' => $data,
            'old' => $link,
            'link' => $data,
            'url' => $link
        ];
    }

    public function send_wechat(Request $request)
    {
        return $request->all();
    }
}