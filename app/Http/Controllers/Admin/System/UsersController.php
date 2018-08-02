<?php
/**
 * Description  CmsTop MediaCloud
 *
 * @Author      liujing <liujing@cmstop.com>
 * @DateTime    2018/8/2 15:18
 * @CopyRight   Beijing CmsTop Technology Co.,Ltd.
 */

namespace App\Http\Controllers\Admin\System;

use App\Http\Services\UserService;
use Illuminate\Routing\Controller;

class UsersController extends Controller
{
    public function index(UserService $service)
    {
        return $service->index();
    }

    public function refresh(UserService $service)
    {
        return $service->refresh();
    }
}