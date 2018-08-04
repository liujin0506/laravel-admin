<?php
/**
 * Description  CmsTop MediaCloud
 *
 * @Author      liujing <liujing@cmstop.com>
 * @DateTime    2018/8/3 17:47
 * @CopyRight   Beijing CmsTop Technology Co.,Ltd.
 */

namespace App\Http\Services;


use App\Models\Aca;

class AcaService extends BaseService
{
    public function index()
    {
        $model = new Aca();
        return $model->tree();
    }

    public function store($params)
    {
        $model = new Aca();
        return $model->add($params);
    }

    public function update($id, $params)
    {
        $model = new Aca();
        return $model->edit($id, $params);
    }

    public function destroy($id)
    {
        $model = new Aca();
        return $model->remove($id);
    }
}