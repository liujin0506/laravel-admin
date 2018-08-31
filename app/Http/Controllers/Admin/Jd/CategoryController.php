<?php
/**
 * Description  CmsTop MediaCloud
 *
 * @Author      liujing <liujing@cmstop.com>
 * @DateTime    2018/8/31 15:13
 * @CopyRight   Beijing CmsTop Technology Co.,Ltd.
 */

namespace App\Http\Controllers\Admin\Jd;

use App\Http\Controllers\Controller;
use App\Http\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(CategoryService $service)
    {
        $params = request()->all();
        return $service->index($params);
    }

    public function update($id, Request $request, CategoryService $service)
    {
        $params = $request->all();
        return $service->update($id, $params);
    }
}