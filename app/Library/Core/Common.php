<?php
/**
 * Description  CmsTop MediaCloud
 *
 * @Author      liujing <liujing@cmstop.com>
 * @DateTime    2018/9/4 17:32
 * @CopyRight   Beijing CmsTop Technology Co.,Ltd.
 */

namespace App\Library\Core;
use App\Library\Jd\Jd;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class Common
{
    public static function getLink($text)
    {
        preg_match('/(http|https):\/\/([\w\d\-_]+[\.\w\d\-_]+)[:\d+]?([\/]?[\w\/\.]+?.*)/i', $text, $matches);
        return isset($matches['0']) ? $matches[0] : '';
    }
    /**
     * 通过推广链接获取skuId
     * @param $url
     * @return string
     */
    public static function getSkuId($url)
    {
        try {
            $client = new Client([
                'allow_redirects' => false,
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.95 Safari/537.36 MicroMessenger/6.5.2.501 NetType/WIFI WindowsWechat QBCore/3.43.901.400 QQBrowser/9.0.2524.400',
                    'Referer' => $url
                ]
            ]);
            $data = $client->get($url, [
                'allow_redirects' => true
            ]);
            $content = $data->getBody()->getContents();
            preg_match('/var hrl=\'(.*)\';var ua/i', $content, $url2);
            $url2 = $url2['1'];
            $data2 = $client->get($url2);
            $location = $data2->getHeader('location');
            $link = $location[0];
            $link_data = parse_url($link);
            $queryParts = explode('&', $link_data['query']);
            $skuId = '';
            foreach ($queryParts as $param) {
                $item = explode('=', $param);
                if ($item[0] == 'sku') {
                    $skuId = $item[1];
                }
            }
            return $skuId;
        } catch (\Exception $e) {
            return '';
        }
    }

    public static function getDetailBySkuId($sku_id)
    {
        $jd = new Jd();
        try {
            $data = $jd->request('jingdong.union.search.queryCouponGoods', [
                'skuIdList' => $sku_id,
            ], 'query_coupon_goods_result');
            if (!$data || $data['total'] == 0) {
                return false;
            }
            $details = $jd->request('jingdong.service.promotion.goodsInfo', [
                'skuIds' => $sku_id
            ], 'getpromotioninfo_result');
            $details = $details['result'][0];
            $details['couponList'] = $data['data'][0]['couponList'];
            $discount = isset($details['couponList'][0]) ? $details['couponList'][0]['discount'] : 0;
            $beginTime = isset($details['couponList'][0]) ? $details['couponList'][0]['beginTime'] : $details['startDate'];
            $endTime = isset($details['couponList'][0]) ? $details['couponList'][0]['endTime'] : $details['endDate'];

            return [
                'cid' => $details['cid'],
                'cid2' => $details['cid2'],
                'cid3' => $details['cid3'],
                'cid_name' => $details['cidName'],
                'cid2_name' => $details['cid2Name'],
                'cid3_name' => $details['cid3Name'],
                'goods_name' => $details['goodsName'],
                'img_url' => $details['imgUrl'],
                'commision_ratio_pc' => $details['commisionRatioPc'],
                'commision_ratio_wl' => $details['commisionRatioWl'],
                'in_order_count' => $details['inOrderCount'],
                'is_free_freight_risk' => $details['isFreeFreightRisk'],
                'is_free_shipping' => $details['isFreeShipping'],
                'is_jd_sale' => $details['isJdSale'],
                'is_seckill' => $details['isSeckill'],
                'material_url' => $details['materialUrl'],
                'shop_id' => $details['shopId'],
                'start_date' => date('Y-m-d H:i:s', $beginTime / 1000),
                'end_date' => date('Y-m-d H:i:s', $endTime / 1000),
                'unit_price' => $details['unitPrice'],
                'wl_unit_price' => $details['wlUnitPrice'],
                'vid' => $details['cid'],
                'discount' => $discount,
                'coupon_list' => $details['couponList'],
                'coupon_num' => count($details['couponList']),
            ];
        } catch (\Exception $e) {
            return false;
        }
    }

    private static function _getUrl($url)
    {
        if (starts_with($url , 'http')) {
            return $url;
        } else {
            return 'https:' . $url;
        }
    }

    public static function getCouponLink($coupons)
    {
        $link = '';
        if (!empty($coupons)) foreach ($coupons as $c) {
            if (!empty($c['link'])) {
                $link = $c['link'];
            }
        }
        return $link ? self::_getUrl($link) : '';
    }
}