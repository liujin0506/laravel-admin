<?php
/**
 * Description  CmsTop MediaCloud
 *
 * @Author      liujing <liujing@cmstop.com>
 * @DateTime    2018/8/31 15:14
 * @CopyRight   Beijing CmsTop Technology Co.,Ltd.
 */

namespace App\Http\Services;

use App\Models\Goods;
use App\Models\Swiper;

class GoodsService extends BaseService
{
    public function index($params)
    {
        $model = new Goods();
        return $model->lists($params);
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
}