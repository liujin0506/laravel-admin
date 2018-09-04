<?php
/**
 * Created by PhpStorm.
 * User: liuji
 * Date: 2018/8/24
 * Time: 22:58
 */

namespace App\Http\Controllers\Wechat\Common;

use App\Http\Controllers\Controller;
use App\Library\Helper\Response;

class ServeController extends Controller
{
    use Response;

    public function serve()
    {
        $app = app('wechat.official_account');
        $app->server->push(function($message){
            return "欢迎关注！";
        });

        return $app->server->serve()->send();
    }
}