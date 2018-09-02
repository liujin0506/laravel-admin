<?php
/**
 * Created by PhpStorm.
 * User: liujing
 * Date: 2018/9/2
 * Time: 下午11:17
 */

namespace App\Library\Auth;


use App\Models\Member;
use Tymon\JWTAuth\Contracts\Providers\Auth;

class MemberAuth implements Auth
{
    /**
     * Check a user's credentials.
     *
     * @param  array  $credentials
     *
     * @return mixed
     */
    public function byCredentials(array $credentials)
    {

    }

    /**
     * Authenticate a user via the id.
     *
     * @param  mixed  $id
     *
     * @return mixed
     */
    public function byId($id)
    {
        return Member::query()->find($id);
    }

    /**
     * Get the currently authenticated user.
     *
     * @return mixed
     */
    public function user()
    {
        return auth('wap')->user();
    }
}