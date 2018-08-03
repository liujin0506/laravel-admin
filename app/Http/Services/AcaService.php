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
use Dingo\Api\Routing\Helpers;

class AcaService
{
    use Helpers;
    public function index()
    {
        $model = new Aca();
        return $model->lists();
    }

    public function store($params)
    {
        $model = new Aca();
        $res = $model->createOrUpdate($params);
        return $this->response->array($res);
    }
}