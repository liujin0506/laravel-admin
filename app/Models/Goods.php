<?php
/**
 * Description  CmsTop MediaCloud
 *
 * @Author      liujing <liujing@cmstop.com>
 * @DateTime    2018/8/30 18:07
 * @CopyRight   Beijing CmsTop Technology Co.,Ltd.
 */

namespace App\Models;

use App\Library\Jd\Jd;
use Illuminate\Database\Eloquent\SoftDeletes;

class Goods extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sort',
        'sku_id',
        'cid',
        'cid2',
        'cid3',
        'cid_name',
        'cid2_name',
        'cid3_name',
        'goods_name',
        'img_url',
        'commision_ratio_pc',
        'commision_ratio_wl',
        'in_order_count',
        'is_free_freight_risk',
        'is_free_shipping',
        'is_jd_sale',
        'is_seckill',
        'material_url',
        'shop_id',
        'start_date',
        'end_date',
        'unit_price',
        'wl_unit_price',
        'vid',
        'discount',
        'coupon_list',
        'coupon_num',
        'is_recommend',
        'slogan',
        'recommend_start',
        'recommend_end'
    ];

    public function lists($params, $columns = ['*'])
    {
        // $columns[] = '(`wl_unit_price` - `discount`) * `commision_ratio_wl` as commision';
        $query = self::query();
        $columns = implode(',', $columns);
        $query->selectRaw($columns . ',greatest((`wl_unit_price` - `discount`), `wl_unit_price`) * `commision_ratio_wl` / 100 as commision');
        $per_page = data_get($params, 'per_page', 20);
        $keyword = data_get($params, 'keyword', '');
        if (!empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->orWhere('goods_name', 'like', '%' . trim($keyword) . '%');
                $q->orWhere('sku_id', $keyword);
            });
        }

        $sku_id = data_get($params, 'sku_id', '');
        if ($sku_id) {
            $query->where('sku_id', $sku_id);
        }

        $is_recommend = data_get($params, 'is_recommend', -1);
        if ($is_recommend >= 0) {
            $query->where('is_recommend', $is_recommend);
            $query->where(function ($q) {
                $q->orWhere('recommend_start', '<=', date('Y-m-d H:i:s'));
                $q->orWhere('recommend_start', null);
            });
            $query->where(function ($q) {
                $q->orWhere('recommend_end', '>=', date('Y-m-d H:i:s'));
                $q->orWhere('recommend_end', null);
            });
        }
        $category_id = data_get($params, 'category_id', 0);
        if ($category_id > 0) {
            $query->where('cid', $category_id);
        }

        $query->where('end_date', '>', date('Y-m-d H:i:s'));
        $query->where('coupon_num', '>', 0);
        $query->where('discount', '>', 0);
        $query->where('commision_ratio_wl', '>', 0);
        $sort_type = data_get($params, 'sort_type', 'common');
        $sort = data_get($params, 'sort', 'desc');
        $sort = $sort == 'asc' ? 'asc' : 'desc';
        if ($sort_type == 'common') {
            $query->orderBy('sort', 'desc');
            $query->orderBy('id', 'desc');
        } elseif ($sort_type == 'price') {
            $query->orderBy('wl_unit_price', $sort);
        } elseif ($sort_type == 'repay') {
            $query->orderBy('commision', $sort);
        }

        $data = $query->paginate($per_page, $columns);
        $data->each(function ($item) {
            if ($item->wl_unit_price - $item->discount > 0) {
                $item->real_price = sprintf("%.2f", $item->wl_unit_price - $item->discount);
            } else {
                $item->real_price = sprintf("%.2f", $item->wl_unit_price);
            }
            $item->end_day = date('m-d', strtotime($item->end_date));
        });
        return $data;
    }

    public function detail($id)
    {
        $item = self::query()->where('id', $id)->first();
        if ($item) {
            if ($item->wl_unit_price - $item->discount > 0) {
                $item->real_price = sprintf("%.2f", $item->wl_unit_price - $item->discount);
            } else {
                $item->real_price = sprintf("%.2f", $item->wl_unit_price);
            }
            $item->end_day = date('m-d', strtotime($item->end_date));
            $item->coupon_list = json_decode($item['coupon_list'], true);
        }
        return $item;
    }

    public function edit($id, $params)
    {
        $params = array_only($params, ['sort', 'is_recommend', 'slogan', 'img_url', 'recommend_end', 'recommend_start']);
        if (!isset($params['slogan'])) {
            $params['slogan'] = '';
        }
        $res = self::query()->where(['id' => $id])->update($params);
        return $res ? self::query()->where('id', $id)->first() : [];
    }

    /**
     * 定时从接口拉取商品
     */
    public function syncGoods($page = 1)
    {
        $jd = new Jd();
        try {
            $data = $jd->request('jingdong.union.search.queryCouponGoods', [
                'pageIndex' => $page,
                'pageSize' => 30
            ], 'query_coupon_goods_result');
            $ids = [];
            if (!empty($data['data'])) foreach ($data['data'] as $value) {
                array_push($ids, $value['skuId']);
            }
            $details = $jd->request('jingdong.service.promotion.goodsInfo', [
                'skuIds' => implode(',', $ids)
            ], 'getpromotioninfo_result');

            $goods = isset($details['result']) ? $details['result'] : [];
            if (!empty($goods)) foreach ($goods as &$value) {
                foreach ($data['data'] as $v) {
                    if ($value['skuId'] == $v['skuId']) {
                        $value['couponList'] = isset($v['couponList']) ? $v['couponList'] : [];
                    }
                }
                $discount = isset($value['couponList'][0]) ? $value['couponList'][0]['discount'] : 0;
                $beginTime = isset($value['couponList'][0]) ? $value['couponList'][0]['beginTime'] : $value['startDate'];
                $endTime = isset($value['couponList'][0]) ? $value['couponList'][0]['endTime'] : $value['endDate'];
                self::query()->updateOrCreate(['sku_id' => $value['skuId']], [
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
                    'start_date' => date('Y-m-d H:i:s', $beginTime / 1000),
                    'end_date' => date('Y-m-d H:i:s', $endTime / 1000),
                    'unit_price' => $value['unitPrice'],
                    'wl_unit_price' => $value['wlUnitPrice'],
                    'vid' => $value['cid'],
                    'discount' => $discount,
                    'coupon_list' => json_encode($value['couponList']),
                    'coupon_num' => count($value['couponList']),
                ]);
            }
            return $goods;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getRand($columns = ['*'])
    {
        // $columns[] = '(`wl_unit_price` - `discount`) * `commision_ratio_wl` as commision';
        $query = self::query();
        $columns = implode(',', $columns);
        $query->selectRaw($columns . ',greatest((`wl_unit_price` - `discount`), `wl_unit_price`) * `commision_ratio_wl` / 100 as commision');

        $query->where('end_date', '>', date('Y-m-d H:i:s'));
        $query->where('coupon_num', '>', 0);
        $query->where('discount', '>', 0);
        $query->where('commision_ratio_wl', '>', 0);

        $query->orderByRaw('rand()');

        $item = $query->first($columns);
        if ($item->wl_unit_price - $item->discount > 0) {
            $item->real_price = sprintf("%.2f", $item->wl_unit_price - $item->discount);
        } else {
            $item->real_price = sprintf("%.2f", $item->wl_unit_price);
        }
        $item->end_day = date('m-d', strtotime($item->end_date));
        $item->coupon_list = json_decode($item->coupon_list, true);
        return $item;
    }
}