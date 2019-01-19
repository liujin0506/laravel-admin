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
use Illuminate\Support\Facades\Log;

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
            $jd = new Jd();
            $skuId = Common::getSkuId($url);
            if (!$skuId) {
                $this->error('链接转换失败');
                return false;
            }
            $detail = Common::getDetailBySkuId($skuId);
            if ($detail) {
                $coupon_url = $this->getCouponLink($detail['coupon_list']);
            } else {
                $detail = Goods::query()->where('sku_id', $skuId)->first();
                $coupon_url = $this->getCouponLink(json_decode($detail['coupon_list'], true));
            }
            if ($detail && $coupon_url) {
                $url_ret = $jd->request('jingdong.service.promotion.coupon.getCodeByUnionId', [
                    'couponUrl' => $coupon_url,
                    'materialIds' => (string) $skuId,
                    'unionId' => $user['union_id']
                ], 'getcodebyunionid_result');
                $url_ret = array_values($url_ret['urlList'])[0];
                if (!$url_ret) {
                    $this->error('获取推广链接失败，请联系管理员');
                }
            } else {
                //
                $url_ret = $jd->request('jingdong.service.promotion.wxsq.getCodeByUnionId', [
                    'proCont' => 1,
                    'materialIds' => (string) $skuId,
                    'unionId' => $user['union_id']
                ], 'getcodebysubunionid_result');
                $url_ret = array_values($url_ret['urlList'])[0];
                if (!$url_ret) {
                    $this->error('获取推广链接失败，请联系管理员');
                }
            }

            $link = str_replace($url, $url_ret, $link);
            return [
                'link' => $link
            ];
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return false;
        }
    }

    public function upload($params)
    {
        if (empty($params) ) {
            $this->error('数据为空，请重新上传');
        }
        $new_arr = collect($params)->chunk(5)->toArray();
        foreach ($new_arr as $value) {
            $this->_upload($value);
        }
        return $params;
    }

    private function _upload($params)
    {
        $skuIds = [];
        foreach ($params as $value) {
            array_push($skuIds, $value['skuId']);
        }
        if (count($skuIds) == 0) {
            $this->error('数据格式不正确，请重新上传');
        }
        if (count($skuIds) > 5) {
            $this->error('为了保证服务器正常运行，单次最多上传5条数据');
        }

        $coupon = [];
        if (!empty($params)) foreach ($params as $value) {
            $coupon[$value['skuId']] = isset($value['优惠券链接']) ? $value['优惠券链接'] : '';
        }
        $jd = new Jd();
        try {
            $details = $jd->request('jingdong.service.promotion.goodsInfo', [
                'skuIds' => implode(',', $skuIds)
            ], 'getpromotioninfo_result');
            $goods = isset($details['result']) ? $details['result'] : [];
            if (!empty($goods)) foreach ($goods as &$value) {
                foreach ($params as $p) {
                    if ($p['skuId'] == $value['skuId']) {
                        $value['discount'] = ($p['京东价'] - $p['券后价']) > 0 ? ($p['京东价'] - $p['券后价']) : 0;
                        $value['unitPrice'] = $p['京东价'];
                        $value['wlUnitPrice'] = $p['京东价'];
                        $value['is_recommend'] = (isset($p['是否推荐']) && $p['是否推荐'] == '是') ? 1 : 0;
                        $value['slogan'] = isset($p['自定义文案']) ? $p['自定义文案'] : '';
                        $value['recommend_start'] = isset($p['京选上架时间']) ? date('Y-m-d H:i:s', strtotime($p['京选上架时间'])) : null;
                        $value['recommend_end'] = isset($p['京选下架时间']) ? date('Y-m-d H:i:s', strtotime($p['京选下架时间'])) : null;
                        $value['sort'] = isset($p['排序']) ? $p['排序'] : 0;
                    }
                }
                if (isset($coupon[$value['skuId']]) && $coupon[$value['skuId']]) {
                    $value['couponList'] = [
                        ['link' => $coupon[$value['skuId']]]
                    ];
                }
                $beginTime = $value['recommend_start'] ?: date('Y-m-d H:i:s', $value['startDate'] / 1000);
                $endTime = $value['recommend_end'] ?: date('Y-m-d H:i:s', $value['endDate'] / 1000);
                Goods::query()->updateOrCreate(['sku_id' => $value['skuId']], [
                    'sort' => $value['sort'],
                    'cid' => $value['cid'],
                    'cid2' => $value['cid2'],
                    'cid3' => $value['cid3'],
                    'cid_name' => $value['cidName'],
                    'cid2_name' => $value['cid2Name'],
                    'cid3_name' => $value['cid3Name'],
                    'goods_name' => $value['goodsName'],
                    'img_url' => $value['imgUrl'],
                    'commision_ratio_pc' => $value['commisionRatioPc'],
                    'commision_ratio_wl' => $value['commisionRatioWl'],
                    'in_order_count' => $value['inOrderCount'],
                    'is_free_freight_risk' => $value['isFreeFreightRisk'],
                    'is_free_shipping' => $value['isFreeShipping'],
                    'is_jd_sale' => $value['isJdSale'],
                    'is_seckill' => $value['isSeckill'],
                    'material_url' => $value['materialUrl'],
                    'shop_id' => $value['shopId'],
                    'start_date' => $beginTime,
                    'end_date' => $endTime,
                    'unit_price' => $value['unitPrice'],
                    'wl_unit_price' => $value['wlUnitPrice'],
                    'vid' => $value['cid'],
                    'discount' => $value['discount'],
                    'coupon_list' => json_encode($value['couponList']),
                    'coupon_num' => count($value['couponList']),
                    'is_recommend' => $value['is_recommend'],
                    'slogan' => $value['slogan'],
                    'recommend_start' => $value['recommend_start'],
                    'recommend_end' => $value['recommend_end']
                ]);
            }
            return $goods;
        } catch (\Exception $e) {
            $this->error($e->getMessage());
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
            $details = $jd->request('jingdong.service.promotion.goodsInfo', [
                'skuIds' => $skuid
            ], 'getpromotioninfo_result');
            if ($details['sucessed'] != 1 || count($details['result']) == 0) {
                $this->error('获取商品信息失败，请检查 skuId ');
            }
            $details = $details['result'][0];
            $details['couponList'] = [
                ['link' => $params['coupon_link']]
            ];
            $discount = $details['wlUnitPrice'] - $params['price'];
            $beginTime = isset($params['recommend_start']) ? strtotime($params['recommend_start']) * 1000 : $details['startDate'];
            $endTime = isset($params['recommend_end']) ? strtotime($params['recommend_end']) * 1000 : $details['startDate'];

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
                $params = array_only($params, [
                    'sort', 'is_recommend', 'img_url', 'slogan', 'recommend_start', 'recommend_end'
                ]);
                if (!isset($params['slogan']) || !$params['slogan']) {
                    $params['slogan'] = '';
                }
                if (!isset($params['img_url']) || !$params['img_url']) {
                    unset($params['img_url']);
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
        $lists = $model->lists($params, [
            'id', 'goods_name', 'img_url', 'wl_unit_price', 'discount', 'commision_ratio_wl', 'end_date'
        ])->toArray();

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

    public function getRand()
    {
        $goodModel = new Goods();
        $detail = $goodModel->getRand()->toArray();
        $jd = new Jd();
        $coupon_url = $this->getCouponLink($detail['coupon_list']);

        $url = $jd->request('jingdong.service.promotion.coupon.getCodeByUnionId', [
            'couponUrl' => $coupon_url,
            'materialIds' => (string) $detail['sku_id'],
            'unionId' => '1000895896'
        ], 'getcodebyunionid_result');
        $url = array_values($url['urlList'])[0];
        if (!$url) {
            $url = $this->getRand();
        }
        return $url;
    }

    public function getRandJson()
    {
        $goodModel = new Goods();
        $detail = $goodModel->getRand()->toArray();
        $jd = new Jd();
        $coupon = $this->getCoupon($detail['coupon_list']);

        $url = $jd->request('jingdong.service.promotion.coupon.getCodeByUnionId', [
            'couponUrl' => $coupon['link'],
            'materialIds' => (string) $detail['sku_id'],
            'unionId' => '1000895896'
        ], 'getcodebyunionid_result');
        $url = array_values($url['urlList'])[0];

        $data = [
            'goods' => [
                'id' => $detail['id'],
                'sku_id' => $detail['sku_id'],
                'goods_name' => $detail['goods_name'],
                'img_url' => $detail['img_url'],
                'wl_unit_price' => $detail['wl_unit_price'],
                'material_url' => $detail['material_url'],
                'discount' => $detail['discount']
            ],
            'conpon' => $coupon,
            'url' => $url
        ];
        if (!$url) {
            $data = $this->getRandJson();
        }
        return $data;
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
                // 尝试重新获取普通推广链接
                /*$url = $jd->request('jingdong.service.promotion.wxsq.getCodeByUnionId', [
                    'proCont' => 1,
                    'materialIds' => (string) $detail['sku_id'],
                    'unionId' => $user['union_id']
                ], 'getcodebysubunionid_result');
                $url = array_values($url['urlList'])[0];
                if (!$url) {
                    $this->error('获取推广链接失败，请联系管理员');
                }*/
		        Goods::query()->where('id', $id)->delete();
		        $this->error('商品优惠活动已经下线，试试其他商品吧~');
            }

            if (empty($detail['slogan'])) {
                $message = '';
                $message .= $detail['goods_name'] . "\n—————————\n";
                $message .= "京东价：¥" . $detail['wl_unit_price'] . "\n";
                $message .= "内购价：¥" . $detail['real_price'] . "\n\n";
                $message .= "👉领券+下单：{$url}";
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
                if ($detail['img_url']) {
                    if (substr($detail['img_url'], 0, 4) === 'http') {
                        $path = storage_path('app') . '/goods_thumb/' . $id . '.png';
                        if (!file_exists($path)) {
                            file_put_contents($path, file_get_contents($detail['img_url']));
                        }
                    } else {
                        $path = storage_path('app') . '/' . $detail['img_url'];
                    }
                    $image = $app->media->uploadImage($path);
                    $app->customer_service->message(new Image($image['media_id']))->to($openid)->send();

                    $getData = [
                        'thumb' => $detail['img_url'],
                        'title' => $detail['goods_name'],
                        'real_price' => $detail['wl_unit_price'],
                        'discount' => $detail['discount'],
                        'new_price' => $detail['real_price'],
                        'url' => $url
                    ];
                    $client = new \GuzzleHttp\Client();
                    $data = $client->post('http://127.0.0.1:5678/html2Image', [
                        'header' => [
                            'Content-Type' => 'application/x-www-form-urlencoded'
                        ],
                        'form_params' => [
                            'url' => 'http://wx.jhz.bjue.cn/poster?' . http_build_query($getData),
                            'type' => 'base64',
                            'width' => 350,
                            'height' => 500
                        ]
                    ]);
                    $data = $data->getBody()->getContents();
                    file_put_contents(storage_path('app') . '/poster.png', base64_decode($data));
                    $image = $app->media->uploadImage(storage_path('app') . '/poster.png');
                    unlink(storage_path('app') . '/poster.png');
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

    private function getCoupon($coupons)
    {
        $coupon = '';
        if (!empty($coupons)) foreach ($coupons as $c) {
            if (!empty($c['link'])) {
                $c['link'] = $this->_getUrl($c['link']);
                $coupon = $c;
            }
        }
        return $coupon;
    }
}
