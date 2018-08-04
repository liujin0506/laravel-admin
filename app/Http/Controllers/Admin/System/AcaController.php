<?php
/**
 * Description  CmsTop MediaCloud
 *
 * @Author      liujing <liujing@cmstop.com>
 * @DateTime    2018/8/3 17:34
 * @CopyRight   Beijing CmsTop Technology Co.,Ltd.
 */

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use App\Http\Services\AcaService;
use Illuminate\Http\Request;

class AcaController extends Controller
{
    public function index(AcaService $service)
    {
        return $service->index();
    }

    public function store(Request $request, AcaService $service)
    {
        $params = $request->all();
        return $service->store($params);
    }

    public function update($id, Request $request, AcaService $service)
    {
        $params = $request->all();
        return $service->update($id, $params);
    }

    public function destroy($id, AcaService $service)
    {
        return $service->destroy($id);
    }
}