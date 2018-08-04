<?php
/**
 * Created by PhpStorm.
 * User: liuji
 * Date: 2018/8/4
 * Time: 17:17
 */

namespace App\Models;

class Role extends Model
{
    protected $fillable =  ['id', 'name', 'remark'];

    /**
     * 获取角色列表
     * @param string $name
     * @param bool $all
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getList($name = "", $all = false)
    {
        $limit = request()->get('per_page', 20);
        $query = self::query();
        if (!empty($name)) {
            $name = addslashes($name);
            $query->where('name', 'like', '%' . $name . '%');
        }
        return $all ? $query->get() : $query->paginate($limit);
    }

    /**
     * 通过id 获取角色信息
     * @param $id
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getRoleNameById($id)
    {
        $query = self::query();
        if (is_array($id)) {
            $query->whereIn('id', $id);
        } else {
            $query->where('id', $id);
        }
        return $query->get(['id as role_id', 'name as role_name']);
    }
}