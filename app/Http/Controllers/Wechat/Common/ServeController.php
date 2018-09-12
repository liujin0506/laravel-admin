<?php
/**
 * Created by PhpStorm.
 * User: liuji
 * Date: 2018/8/24
 * Time: 22:58
 */

namespace App\Http\Controllers\Wechat\Common;

use App\Http\Controllers\Controller;
use App\Library\Helper\Response;

class ServeController extends Controller
{
    use Response;

    public function serve()
    {
        $app = app('wechat.official_account');
        $app->server->push(function($message){
            return <<<EOT
『京好赚』是京东西北官方返佣福利平台
💰自购省钱，分享赚钱💰

【京选】精选高佣低价爆款，专享优惠券产品
【找货】海量商品，随意挑选
【转链】智能转链，将商品链接、文案自动转成二合一+二维码海报图片，方便分享！
【我的】查看佣金、推广攻略，完成大咖转变之路！🚀

😄开启您的赚钱之旅吧！😄
EOT;

        });

        return $app->server->serve()->send();
    }
}