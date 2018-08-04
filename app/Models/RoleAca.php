<?php
/**
 * Description  CmsTop MediaCloud
 *
 * @Author      liujing <liujing@cmstop.com>
 * @DateTime    2018/5/10 14:30
 * @CopyRight   Beijing CmsTop Technology Co.,Ltd.
 */

namespace App\Models;

use Illuminate\Support\Facades\Cache;

class RoleAca extends Model
{
    public $timestamps = false;
    protected $fillable =  ['id', 'role_id', 'aca_id'];
    protected $table = 'roles_aca';

    /**
     * 获取角色对应的权限
     * @param $role_id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|static[]
     */
    public function getAca($role_id)
    {
        $query = self::query();
        $aca = $query->where('role_id', $role_id)
            ->leftJoin('aca', 'roles_aca.aca_id', '=', 'aca.id')
            ->get(['aca.name as aca_name', 'aca.id as aca_id'])
            ->toArray();
        return $aca;
    }

    /**
     * 设置角色权限
     * @param $role_id
     * @param array $acaIds
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|static[]
     */
    public function setAca($role_id, $acaIds = [])
    {
        $query = self::query();
        $insert = [];
        if (!empty($acaIds)) foreach ($acaIds as $v) {
            array_push($insert, [
               'role_id' => $role_id,
               'aca_id' => $v
            ]);
        }
        $query->where('role_id', $role_id)->delete();
        if (!empty($insert)) {
            // 删除权限和角色相关的缓存
            Cache::tags(['aca', 'role'])->flush();
            self::query()->insert($insert);
        }
        return $this->getAca($role_id);
    }
}