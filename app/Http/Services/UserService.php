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
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserService extends BaseService
{

    public function index($params)
    {
        $model = new User();
        return $model->lists($params);
    }

    public function logout()
    {
        return auth('web')->logout();
    }

    public function detail()
    {
        $user = auth('web')->user();
        $user['roles'] = ['admin'];
        return $this->response->array($user);
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
            throw new BadRequestHttpException('角色不能为空');
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