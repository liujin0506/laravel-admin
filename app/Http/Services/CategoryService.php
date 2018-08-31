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
}