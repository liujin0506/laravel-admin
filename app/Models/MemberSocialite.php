<?php
/**
 * Description  CmsTop MediaCloud
 *
 * @Author      liujing <liujing@cmstop.com>
 * @DateTime    2018/8/29 15:13
 * @CopyRight   Beijing CmsTop Technology Co.,Ltd.
 */

namespace App\Models;

use Symfony\Component\HttpKernel\Exception\HttpException;

class MemberSocialite extends Model
{
    protected $fillable =  ['provider', 'member_id', 'openid', 'name', 'nickname', 'avatar', 'email'];

    /**
     * @param $params
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|\Illuminate\Contracts\Auth\Authenticatable
     */
    public function Wechat($params)
    {
        $provider = __FUNCTION__;
        $openid = data_get($params, 'openid');
        $data = array_only($params, ['name', 'nickname', 'avatar', 'email']);
        $res = self::query()->updateOrCreate(['openid' => $openid, 'provider' => $provider], $data);
        if (!$res) {
            throw new HttpException(400, "Socialite Login Faild.");
        }
        if (!$res->member_id) { // 如果没有管理用户，创建用户并关联
            try {
                $this->getConnection()->beginTransaction();
                $member = Member::query()->create($data);
                $member_id = $member->id;
                self::query()->where('id', $res->id)->update(['member_id' => $member_id]);
                $this->getConnection()->commit();
                return $member;
            } catch (\Exception $e) {
                $this->getConnection()->rollBack();
                throw new HttpException(400, $e->getMessage());
            }
        } else {
            return Member::query()->where('id', $res->member_id)->first();
        }
    }
}