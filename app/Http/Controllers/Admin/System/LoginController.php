<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use App\Http\Services\UserService;
use App\Library\Helper\Response;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class LoginController extends Controller
{
    use ThrottlesLogins,Response;

    public function login(Request $request)
    {
        try {
            $this->validateLogin($request);
            $params = $request->only('username', 'password');
            $token = auth('web')->attempt($params);
            if (!$token) {
                throw new HttpException(400, '登陆失败，请检查用户名和密码');
            }
            return [
               'token' => $token
            ];
        } catch (\Exception $e) {
            throw new HttpException(400, $e->getMessage());
        }

    }

    public function logout(UserService $service)
    {
        return $service->logout();
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
    }
}