<?php

namespace App\Models;

use App\Library\Jd\Jd;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Member extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'nickname', 'nickname', 'avatar', 'email', 'mobile', 'union_id'
    ];

    public function lists($params)
    {
        $limit = request()->get('per_page', 20);
        $query = self::query();

        $name = data_get($params, 'name');
        if ($name) {
            $query->where(function ($q) use ($name) {
                $q->where('username', 'like', '%' . addslashes($name) . '%');
                $q->orWhere('nickname', 'like', '%' . addslashes($name) . '%');
            });
        }
        $list = $query->paginate($limit);
        return $list;
    }

    public function setUnionId($id, $union_id)
    {
        // 判断union_id 是否可用， 尝试通过此联盟id获取推广链接
        $jd = new Jd();
        $skuId = Goods::query()->orderBy('id', 'desc')->value('sku_id');
        try {
            $result = $jd->request('jingdong.service.promotion.wxsq.getCodeByUnionId', [
                'proCont' => 1,
                'materialIds' => $skuId,
                'unionId' => $union_id
            ], 'getcodebysubunionid_result');
            if (!$result || $result['resultCode'] != '0') {
                throw new \Exception('联盟ID错误，请核对～');
            }
            $item = self::query()->where('id', $id)->first();
            if (!$item) {
                throw new \Exception('用户信息不存在～');
            }
            if ($item['union_id'] == $union_id) {
                throw new \Exception('您已经绑定过此联盟ID, 请勿重复绑定！');
            }
            return self::query()->where('id', $id)->update(['union_id' => $union_id]);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'openid' => $GLOBALS['openid']
        ];
    }
}
