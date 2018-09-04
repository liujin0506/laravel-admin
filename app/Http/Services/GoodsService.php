<?php
/**
 * Description  CmsTop MediaCloud
 *
 * @Author      liujing <liujing@cmstop.com>
 * @DateTime    2018/8/31 15:14
 * @CopyRight   Beijing CmsTop Technology Co.,Ltd.
 */

namespace App\Http\Services;

use App\Library\Core\Common;
use App\Library\Jd\Jd;
use App\Models\Goods;
use App\Models\Swiper;
use EasyWeChat\Kernel\Messages\Image;

class GoodsService extends BaseService
{
    public function index($params)
    {
        $model = new Goods();
        return $model->lists($params);
    }

    public function transLink($params, $user)
    {
        $link = data_get($params, 'link', '');
        $url = Common::getLink($link);
        if (!$url) {
            $this->error('链接获取失败');
            return false;
        }
        try {
            $skuId = Common::getSkuId($url);
            if (!$skuId) {
                $this->error('链接转换失败');
                return false;
            }
            $detail = Common::getDetailBySkuId($skuId);
            $jd = new Jd();
            $coupon_url = $this->getCouponLink($detail['coupon_list']);
            $url_ret = $jd->request('jingdong.service.promotion.coupon.getCodeByUnionId', [
                'couponUrl' => $coupon_url,
                'materialIds' => (string) $skuId,
                'unionId' => $user['union_id']
            ], 'getcodebyunionid_result');
            $url_ret = array_values($url_ret['urlList'])[0];
            if (!$url_ret) {
                $this->error('获取推广链接失败，请联系管理员');
            }
            $link = str_replace($url, $url_ret, $link);
            return [
                'link' => $link
            ];
        } catch (\Exception $e) {
            $this->error('链接转换失败');
            return false;
        }
    }

    public function create($params)
    {
        $skuid = data_get($params, 'sku_id', '');
        if (!$skuid) {
            $this->error('商品 skuId 不能为空');
            return false;
        }
        $jd = new Jd();
        try {
            $data = $jd->request('jingdong.union.search.queryCouponGoods', [
                'skuIdList' => $skuid,
            ], 'query_coupon_goods_result');
            if (!$data || $data['total'] == 0) {
                $this->error('未找到商品, 请检查 skuId');
            }
            $details = $jd->request('jingdong.service.promotion.goodsInfo', [
                'skuIds' => $skuid
            ], 'getpromotioninfo_result');
            $details = $details['result'][0];
            $details['couponList'] = $data['data'][0]['couponList'];
            $discount = isset($details['couponList'][0]) ? $details['couponList'][0]['discount'] : 0;
            $beginTime = isset($details['couponList'][0]) ? $details['couponList'][0]['beginTime'] : $details['startDate'];
            $endTime = isset($details['couponList'][0]) ? $details['couponList'][0]['endTime'] : $details['endDate'];

            $res = Goods::query()->updateOrCreate(['sku_id' => $skuid], [
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
                'coupon_list' => json_encode($details['couponList']),
                'coupon_num' => count($details['couponList']),
            ]);
            if ($res) {
                $params = array_only($params, ['sort', 'is_recommend', 'slogan', 'ad', 'ad_qr']);
                if (!$params['ad']) {
                    $params['ad'] = '';
                }
                if (isset($params['slogan']) && !$params['slogan']) {
                    $params['slogan'] = '';
                }
                if (!$params['ad_qr']) {
                    $params['ad_qr'] = '';
                }
                Goods::query()->where(['sku_id' => $skuid])->update($params);
                return $res;
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return false;
        }
    }

    public function update($id, $params)
    {
        $model = new Goods();
        return $model->edit($id, $params);
    }

    public function wechatIndex($params)
    {
        $page = data_get($params, 'page', 1);
        $keyword = data_get($params, 'keyword', '');
        $model = new Goods();
        $lists = $model->lists($params, ['id', 'goods_name', 'img_url', 'wl_unit_price', 'discount', 'commision_ratio_wl', 'end_date'])->toArray();

        // 第一页并且不是搜索返回幻灯片信息
        if ($page == 1 && !$keyword) {
            $swiperModel = new Swiper();
            $swiper = $swiperModel->getList([], true);
            $lists['swiper'] = $swiper;
        }
        return $lists;
    }

    public function detail($id)
    {
        $model = new Goods();

        return $model->detail($id);
    }

    public function sendWechat($openid, $params)
    {
        $link = data_get($params, 'link');
        if (!$link) {
            $this->error('内容不能为空');
        }
        $app = app('wechat.official_account');
        try {
            $res = $app->customer_service->message($link)->to($openid)->send();
            if ($res['errcode'] == 0) {
                return $res;
            } else {
                $this->error('推广失败，请联系客服' . json_encode($res));
            }
            return $res;
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return false;
        }
    }

    public function spread($id, $openid, $params)
    {
        $app = app('wechat.official_account');
        try {
            $goodModel = new Goods();
            $detail = $goodModel->detail($id)->toArray();
            $user = auth('wap')->user();
            $jd = new Jd();
            $coupon_url = $this->getCouponLink($detail['coupon_list']);
            $url = $jd->request('jingdong.service.promotion.coupon.getCodeByUnionId', [
                'couponUrl' => $coupon_url,
                'materialIds' => (string) $detail['sku_id'],
                'unionId' => $user['union_id']
            ], 'getcodebyunionid_result');
            $url = array_values($url['urlList'])[0];
            if (!$url) {
                $this->error('获取推广链接失败，请联系管理员');
            }

            if (empty($detail['slogan'])) {
                $message = '';
                $message .= $detail['goods_name'] . "\n—————————\n";
                $message .= "京东价：¥" . $detail['wl_unit_price'] . "\n";
                $message .= "内购价：¥" . $detail['real_price'] . "\n\n";
                $message .= "👉领券+下单：<a href='{$url}'>{$url}</a>";
            } else {
                $message = str_replace(['[title]', '[price]', '[realprice]', '[link]'], [
                    $detail['goods_name'],
                    $detail['wl_unit_price'],
                    $detail['real_price'],
                    $url
                ], $detail['slogan']);
            }
            $res = $app->customer_service->message($message)->to($openid)->send();
            if ($res['errcode'] == 0) {
                // 发送图片
                if ($detail['ad']) {
                    $path = storage_path('app') . '/' . $detail['ad'];
                    $image = $app->media->uploadImage($path);
                    $app->customer_service->message(new Image($image['media_id']))->to($openid)->send();
                }
                if ($detail['ad_qr']) {
                    $path = storage_path('app') . '/' . $detail['ad_qr'];
                    $image = $app->media->uploadImage($path);
                    $app->customer_service->message(new Image($image['media_id']))->to($openid)->send();
                }
            } else {
                $this->error('推广失败，请联系客服' . json_encode($res));
            }
            return $res;
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return false;
        }
    }

    private function _getUrl($url)
    {
        if (starts_with($url , 'http')) {
            return $url;
        } else {
            return 'https:' . $url;
        }
    }

    private function getCouponLink($coupons)
    {
        $link = '';
        if (!empty($coupons)) foreach ($coupons as $c) {
            if (!empty($c['link'])) {
                $link = $c['link'];
            }
        }
        return $link ? $this->_getUrl($link) : '';
    }
}