<?php
/**
 * Description  CmsTop MediaCloud
 *
 * @Author      liujing <liujing@cmstop.com>
 * @DateTime    2018/9/3 9:31
 * @CopyRight   Beijing CmsTop Technology Co.,Ltd.
 */

namespace App\Http\Controllers\Wechat\Goods;


use App\Http\Controllers\Controller;
use App\Http\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request, CategoryService $service)
    {
        return $service->mobile_list($request->all());
    }
}