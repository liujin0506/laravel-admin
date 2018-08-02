<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Services\UserService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    public function login(Request $request, UserService $service){
        return $service->login($request);
    }

    public function logout(UserService $service){
        return $service->logout();
    }
}