<?php
/**
 * Description  CmsTop MediaCloud
 *
 * @Author      liujing <liujing@cmstop.com>
 * @DateTime    2018/8/31 15:14
 * @CopyRight   Beijing CmsTop Technology Co.,Ltd.
 */

namespace App\Http\Services;


use App\Models\Category;

class CategoryService extends BaseService
{
    public function index($params)
    {
        $model = new Category();
        return $model->lists($params);
    }

    public function update($id, $params)
    {
        $model = new Category();
        return $model->edit($id, $params);
    }

    /**
     * 移动端菜单列表
     * @param $params
     */
    public function mobile_list($params)
    {
        $model = new Category();
        return $model->lists($params);
    }
}