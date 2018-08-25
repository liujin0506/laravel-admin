<?php
/**
 * Created by PhpStorm.
 * User: liuji
 * Date: 2018/8/24
 * Time: 22:58
 */

namespace App\Http\Controllers\Admin\Wechat;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WeChatController extends Controller
{
    public function serve()
    {
        $app = app('wechat.official_account');
        $app->server->push(function($message){
            return "欢迎关注 overtrue！";
        });

        return $app->server->serve()->send();
    }

    public function index(Request $request)
    {
        $officialAccount = app('wechat.official_account');
        $user = $officialAccount->oauth->user();
        // $res = $officialAccount->oauth->scopes(['snsapi_userinfo'])->redirect($request->fullUrl())->getTargetUrl();
        dd($user);
    }
}