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
use App\Models\UserRole;
use Dingo\Api\Routing\Helpers;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;

class UserService extends BaseService
{
    use AuthenticatesUsers;

    public function index($params)
    {
        $model = new User();
        return $model->lists($params);
    }

    public function login($request)
    {
        $user = User::where('email', $request->username)->orWhere('username', $request->username)->first();
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

    public function detail()
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

    /**
     * 获取管理员角色
     * @param $custom_id
     * @return mixed
     */
    public function getRoles($custom_id)
    {
        $model = new UserRole();
        return $model->getRoles($custom_id);
    }

    /**
     * 设置管理员角色
     * @param $custom_id
     * @param array $roleIds
     * @return mixed|void
     */
    public function setRoles($custom_id, $roleIds = [])
    {
        $model = new UserRole();
        if (empty($roleIds)) {
            return $this->response->errorBadRequest('角色不能为空');
        } else {
            $model->setRoles($custom_id, $roleIds);
            return $this->getRoles($custom_id);
        }
    }

    public function changePassword($id, $params)
    {
        return $params;
    }
}