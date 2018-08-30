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
use App\Library\Jd\Jd;
use App\Models\Category;
use App\Models\Goods;

class GoodsController extends Controller
{
    public function lists()
    {
        $jd = new Jd();
        try {
//            $data = $jd->request("jingdong.UnionThemeGoodsService.queryCouponGoods", [
//                'from' => 0,
//                'pageSize' => 20
//            ], 'queryCouponGoods_result');
//            $data = $jd->request('jingdong.union.search.queryCouponGoods', [
//                'pageIndex' => 1,
//                'pageSize' => 10
//            ], 'query_coupon_goods_result');

//            $link = $jd->request('jingdong.service.promotion.coupon.getCodeByUnionId', [
//                'couponUrl' => urlencode("https://coupon.jd.com/ilink/couponSendFront/send_index.action?key=37b66742e8744ab78ed46cc7ec73331c&roleId=13782746&to=yuandi.jd.com"),
//                'materialIds' => '29768024852',
//                'unionId' => '1000927922'
//            ], 'getcodebyunionid_result');
//            return $link;
            $model = new Goods();
            dd($model->syncGoods());
        } catch (\Exception $e) {
            dd($e);
        }
    }
}