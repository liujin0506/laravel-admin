<?php
/**
 * Created by PhpStorm.
 * User: liujing
 * Date: 2018/9/2
 * Time: 下午9:01
 */

namespace App\Http\Services;


use App\Models\Member;

class MemberService extends BaseService
{
    public function bindUnionId($uid, $union_id)
    {
        $model = new Member();
        try {
            return $model->setUnionId($uid, $union_id);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return false;
        }
    }
}