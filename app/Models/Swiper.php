<?php
/**
 * Description  CmsTop MediaCloud
 *
 * @Author      liujing <liujing@cmstop.com>
 * @DateTime    2018/8/31 17:32
 * @CopyRight   Beijing CmsTop Technology Co.,Ltd.
 */

namespace App\Models;


use Illuminate\Database\Eloquent\SoftDeletes;

class Swiper extends Model
{
    use SoftDeletes;

    protected $fillable = ['sort', 'area_id', 'title', 'link', 'status'];

    /**
     * 获取幻灯片列表
     * @param $params
     * @param bool $all
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getList($params, $all = false)
    {
        $limit = data_get($params, 'per_page', 20);
        $query = self::query();
        if ($title = data_get($params, 'title')) {
            $query->where('title', 'like', '%' . trim($title) . '%');
        }
        $area_id = data_get($params, 'area_id', 0);
        if ($area_id > 0) {
            $query->where('area_id', $area_id);
        }
        return $all ? $query->get() : $query->paginate($limit);
    }
}