<?php
/**
 * Description  CmsTop MediaCloud
 *
 * @Author      liujing <liujing@cmstop.com>
 * @DateTime    2018/8/2 16:59
 * @CopyRight   Beijing CmsTop Technology Co.,Ltd.
 */

namespace App\Models;

use Illuminate\Support\Facades\Cache;

class UserRole extends Model
{
    public $timestamps = false;
    protected $fillable =  ['id', 'role_id', 'user_id'];

    /**
     * 获取用户对应的角色
     * @param $roleid
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|static[]
     */
    public function getRoles($user_id)
    {
        $query = self::query();
        if (is_array($user_id)) {
            $query->whereIn('user_id', $user_id);
        } else {
            $query->where('user_id', $user_id);
        }
        $roles = $query->leftJoin('roles', 'user_roles.role_id', '=', 'roles.id')
            ->get(['user_id', 'roles.id', 'roles.name', 'roles.remark']);
        return $roles;
    }

    /**
     * 获取管理员对应的角色id
     * @param $user_id
     * @return array
     */
    public function getUserRoleIds($user_id)
    {
        if (!$user_id) {
            return [];
        }
        $key = sprintf("%s:%s", __FUNCTION__, $user_id);
        $tags = ['role', 'role_' . $user_id];
        // 缓存管理员的角色信息
        $data = Cache::tags($tags)->rememberForever($key, function () use ($user_id) {
            return self::query()
                ->where('user_id', $user_id)
                ->pluck('role_id')->toArray();
        });
        return $data ?: [];
    }

    /**
     * 设置管理员角色
     * @param $user_id
     * @param array $roleIds
     * @return bool
     */
    public function setRoles($user_id, $roleIds = [])
    {
        $query = self::query();
        $insert = [];
        if (!empty($roleIds)) foreach ($roleIds as $v) {
            if (!$v) {
                continue;
            }
            array_push($insert, [
                'user_id' => $user_id,
                'role_id' => $v
            ]);
        }
        $query->where('user_id', $user_id)->delete();
        if (!empty($insert)) {
            // 删除权限和角色相关的缓存
            Cache::tags('role_' . $user_id)->flush();
            self::query()->insert($insert);
        }
        return $this->getRolesByUserId($user_id);
    }

    /**
     * 根据用户id获取角色(缓存)
     * 编辑角色和用户角色需要清除缓存
     * @param $user_id
     * @return mixed
     */
    public function getRolesByUserId($user_id)
    {
        $key = sprintf("%s:%s", __FUNCTION__, $user_id);
        $tags = ['role', 'role_' . $user_id];
        return Cache::tags($tags)->rememberForever($key, function () use ($user_id) {
            return self::query()
                ->where('user_id', $user_id)
                ->leftJoin('roles', 'user_roles.role_id', '=', 'roles.id')
                ->orderBy('id', 'asc')
                ->get(['roles.id', 'roles.name'])->toArray();
        });
    }
}