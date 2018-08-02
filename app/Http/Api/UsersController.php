<?php
/**
 * Description  CmsTop MediaCloud
 *
 * @Author      liujing <liujing@cmstop.com>
 * @DateTime    2018/8/2 15:18
 * @CopyRight   Beijing CmsTop Technology Co.,Ltd.
 */

namespace App\Http\Api;

use Dingo\Api\Routing\Helpers;
use Illuminate\Routing\Controller;

class UsersController extends Controller
{
    use Helpers;

    public function __construct()
    {
        $this->middleware('api.auth');
    }
    public function index(){
        $user = $this->auth->user();

        return $user;
    }
}