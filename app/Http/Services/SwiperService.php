<?php
/**
 * Created by PhpStorm.
 * User: liuji
 * Date: 2018/8/4
 * Time: 17:15
 */

namespace App\Http\Services;

use App\Models\Swiper;

class SwiperService extends BaseService
{
    public function index($params, $all = false)
    {
        $model = new Swiper();
        return $model->getList($params, $all);
    }

    public function store($params)
    {
        if ($ret = Swiper::query()->create($params)) {
            return $ret;
        } else {
            $this->error('创建失败');
            return false;
        }

    }

    public function update($id, $params)
    {
        if ($ret = Swiper::query()->where('id', $id)->update($params)) {
            return $ret;
        } else {
            $this->error('更新失败');
            return false;
        }
    }

    public function destroy($id)
    {
        if ($ret = Swiper::destroy($id)) {
            return $ret;
        } else {
            $this->error('删除失败');
            return false;
        }
    }
}