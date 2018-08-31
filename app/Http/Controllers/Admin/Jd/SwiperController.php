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
use App\Http\Services\SwiperService;
use Illuminate\Http\Request;

class SwiperController extends Controller
{
    public function index(SwiperService $service)
    {
        $params = request()->all();
        return $service->index($params);
    }

    public function store(Request $request, SwiperService $service)
    {
        $params = $request->all();
        return $service->store($params);
    }

    public function update($id, Request $request, SwiperService $service)
    {
        $params = $request->all();
        return $service->update($id, $params);
    }

    public function destroy($id, SwiperService $service)
    {
        return $service->destroy($id);
    }
}