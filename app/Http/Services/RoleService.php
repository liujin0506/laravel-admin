<?php
/**
 * Created by PhpStorm.
 * User: liuji
 * Date: 2018/8/4
 * Time: 17:15
 */

namespace App\Http\Services;

use App\Models\Role;
use App\Models\RoleAca;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class RoleService extends BaseService
{
    public function index($name = '')
    {
        $model = new Role();
        return $model->getList($name);
    }

    public function store($params)
    {
        if ($ret = Role::query()->create($params)) {
            Cache::tags('role')->flush();
            return $ret;
        } else {
            throw new BadRequestHttpException('角色创建失败');
        }

    }

    public function update($id, $params)
    {
        if ($ret = Role::query()->where('id', $id)->update($params)) {
            Cache::tags('role')->flush();
            return $ret;
        } else {
            throw new BadRequestHttpException('角色更新失败');
        }
    }

    public function destroy($id)
    {
        if ($ret = Role::destroy($id)) {
            Cache::tags('role')->flush();
            return $ret;
        } else {
            throw new BadRequestHttpException('角色删除失败');
        }
    }

    /**
     * 获取角色的权限集
     * @param $id
     * @return mixed
     */
    public function getAca($id)
    {
        $model = new RoleAca();
        return $model->getAca($id);
    }

    /**
     * 设置角色权限
     * @param $id
     * @param array $acaIds
     * @return RoleAca[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public function setAca($id, $acaIds = [])
    {
        $model = new RoleAca();
        if (empty($acaIds)) {
            throw new BadRequestHttpException('权限集不能为空');
        } else {
            return $model->setAca($id, $acaIds);
        }
    }
}