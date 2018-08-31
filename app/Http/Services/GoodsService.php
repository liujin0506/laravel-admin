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
}