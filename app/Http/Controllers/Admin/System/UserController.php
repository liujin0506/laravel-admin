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
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class UserController extends Controller
{
    public function index(Request $request, UserService $service)
    {
        $params = $request->all();
        return $service->index($params);
    }

    public function detail(UserService $service)
    {
        return $service->detail();
    }

    public function update($id, Request $request, UserService $service)
    {
        return $service->update($id, $request->all());
    }

    public function get_roles($id, UserService $service)
    {
        return $service->getRoles($id);
    }

    public function set_roles($id, UserService $service, Request $request)
    {
        $ids = $request->post('items');
        return $service->setRoles($id, $ids);
    }

    public function change_password($id, UserService $service, Request $request)
    {
        $params = $request->all();
        return $service->changePassword($id, $params);
    }

    public function refresh(UserService $service)
    {
        return $service->refresh();
    }
}