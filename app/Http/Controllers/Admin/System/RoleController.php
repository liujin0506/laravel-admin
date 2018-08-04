<?php
/**
 * Created by PhpStorm.
 * User: liuji
 * Date: 2018/8/4
 * Time: 17:14
 */

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use App\Http\Services\RoleService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index(Request $request, RoleService $service)
    {
        $name = $request->get('name', '');
        return $service->index($name);
    }

    public function store(Request $request, RoleService $service)
    {
        $params = $request->all();
        return $service->store($params);
    }

    public function update($id, Request $request, RoleService $service)
    {
        $params = $request->all();
        return $service->update($id, $params);
    }

    public function destroy($id, RoleService $service)
    {
        return $service->destroy($id);
    }

    public function get_aca($id, RoleService $service)
    {
        return $service->getAca($id);
    }

    public function set_aca($id, Request $request, RoleService $service)
    {
        $ids = $request->get('items');
        return $service->setAca($id, $ids);
    }
}