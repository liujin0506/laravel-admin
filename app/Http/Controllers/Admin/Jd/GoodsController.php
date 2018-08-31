<?php
/**
 * Description  CmsTop MediaCloud
 *
 * @Author      liujing <liujing@cmstop.com>
 * @DateTime    2018/8/31 16:22
 * @CopyRight   Beijing CmsTop Technology Co.,Ltd.
 */

namespace App\Http\Controllers\Admin\Jd;

use App\Http\Controllers\Controller;
use App\Http\Services\GoodsService;
use Illuminate\Http\Request;

class GoodsController extends Controller
{
    public function index(GoodsService $service)
    {
        $params = request()->all();
        return $service->index($params);
    }

    public function update($id, Request $request, GoodsService $service)
    {
        $params = $request->all();
        return $service->update($id, $params);
    }
}