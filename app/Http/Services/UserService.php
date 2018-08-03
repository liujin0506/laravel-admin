<?php
/**
 * Description  CmsTop MediaCloud
 *
 * @Author      liujing <liujing@cmstop.com>
 * @DateTime    2018/8/2 17:30
 * @CopyRight   Beijing CmsTop Technology Co.,Ltd.
 */

namespace App\Http\Services;

use App\Models\User;
use Dingo\Api\Routing\Helpers;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;

class UserService
{
    use Helpers;
    use AuthenticatesUsers;

    public function login($request)
    {
        $user = User::where('email', $request->username)->orWhere('name', $request->username)->first();
        if ($user && Hash::check($request->get('password'), $user->password)) {
            $token = JWTAuth::fromUser($user);
            $this->clearLoginAttempts($request);
            return $this->response->array([
                'token' => $token,
                'message' => 'User Authenticated'
            ]);
        }
        return $this->response->errorBadRequest('用户名或密码不正确');
    }

    public function logout()
    {
        return $this->guard()->logout();
    }

    public function index()
    {
        $user = $this->auth->user();
        $user['roles'] = ['admin'];
        return $user;
    }

    public function refresh()
    {
        $old_token = JWTAuth::gettoken();
        $new_token = JWTAuth::refresh($old_token); // 刷新token并返回
        JWTAuth::invalidate($old_token); // 销毁过期token
        return $this->response->array([
            'token' => $new_token,
            'status_code' => 201
        ]);
    }
}