<?php

namespace App\Models;

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
        'name', 'nickname', 'nickname', 'avatar', 'email', 'mobile'
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
        return [];
    }
}
