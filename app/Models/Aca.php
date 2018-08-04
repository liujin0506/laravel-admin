<?php
/**
 * Description  CmsTop MediaCloud
 *
 * @Author      liujing <liujing@cmstop.com>
 * @DateTime    2018/8/2 16:51
 * @CopyRight   Beijing CmsTop Technology Co.,Ltd.
 */

namespace App\Models;
use App\Library\Core\Tree;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Kalnoy\Nestedset\NodeTrait;

class Aca extends Model
{
    public $timestamps = false;
    protected $fillable =  ['id', 'parent_id', 'route_name', 'apis', 'name', 'remark', 'public_aca'];
    protected $table = 'aca';

    /**
     * 权限集列表
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function lists()
    {
        $query = self::query();
        return $query->get();
    }

    /**
     * 获取树形菜单
     * @return array
     */
    public function tree()
    {
        $data = $this->lists()->toArray();
        return Tree::listToTree($data, 'id', 'parent_id');
    }

    /**
     * 创建
     * @param $params
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|int
     */
    public function add($params)
    {
        $params = array_only($params, $this->fillable);
        $res = self::query()->create($params);
        if ($res) {
            // 删除权限缓存
            Cache::tags('aca')->flush();
        }
        return $res;
    }

    public function edit($id, $params)
    {
        $params = array_only($params, $this->fillable);
        $res = self::query()->where(['id' => $id])->update($params);
        if ($res) {
            // 删除权限缓存
            Cache::tags('aca')->flush();
        }
        return $res;
    }

    /**
     * 删除权限集
     * @param $id
     * @return mixed
     */
    public function remove($id)
    {
        Cache::tags('aca')->flush();
        return self::query()->where(['id' => $id])->delete();
    }

    /**
     * 获取管理员权限集
     * @param int $user_id
     * @param string $gate
     * @return mixed
     */
    public function getUserAca($user_id = 0)
    {
        $key = sprintf("%s:%s", __FUNCTION__, $user_id);
        // 缓存权限
        return Cache::tags('aca')->rememberForever($key, function () use ($user_id) {
            $userRolesModel = new UserRole();
            $roles = $userRolesModel->getUserRoleIds($user_id);

            $query = self::query()->leftJoin('roles_aca', 'roles_aca.aca_id', '=', 'aca.id')
                ->where(function (Builder $q) use ($roles) {
                    if (is_array($roles)) {
                        $q->orWhereIn('roles_aca.role_id', $roles);
                    } else {
                        $q->orWhere('roles_aca.role_id', $roles);
                    }
                    $q->orWhere('aca.public_aca', '=', 1);
                })
                ->orderBy('id', 'asc')
                ->distinct();
            $aca = $query->get([
                'aca.id', 'aca.parent_id', 'aca.name', 'aca.route_name',
                'aca.public_aca','aca.apis'
            ])->toArray();
            $parent_id = $this->_getParentId($aca);
            do {
                $new_arr = $this->_getParent($parent_id);
                $parent_id = $this->_getParentId($new_arr);
                $aca = array_merge($aca, $new_arr);
            } while ($parent_id);
            return $aca;
        });
    }

    private function _getParentId($aca)
    {
        $parent_id = [];
        if ($aca) {
            $parent_id = collect($aca)->pluck('parent_id')->toArray();
            $parent_id = array_filter(array_unique($parent_id), function ($item){
                return $item > 0;
            });
        }
        return $parent_id;
    }

    private function _getParent($parent_id)
    {
        $query = self::query();
        $new_arr = $query->whereIn('id', $parent_id)
            ->get(['id', 'parent_id', 'name', 'route_name', 'public_aca', 'apis'])
            ->toArray();
        return $new_arr;
    }
}