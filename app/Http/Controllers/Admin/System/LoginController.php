<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use App\Http\Services\UserService;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use ThrottlesLogins;

    public function login(Request $request)
    {
        try {
            $this->validateLogin($request);
            $params = $request->only('email', 'password');
            $token = auth('web')->attempt($params);
            return [
               'token' => $token
            ];
        } catch (\Exception $e) {
            return $e->getMessage();
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
            'email' => 'required|string',
            'password' => 'required|string',
        ]);
    }
}