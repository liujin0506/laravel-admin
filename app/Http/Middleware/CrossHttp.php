<?php
/**
 * Created by PhpStorm.
 * User: SYSTEM
 * Date: 2018/8/2
 * Time: 22:55
 */

namespace App\Http\Middleware;

use Closure;

class CrossHttp
{
    public function handle($request, Closure $next) {
        $response = $next($request);
        // 调试模式支持跨域
        if (config('app.debug')) {
            $response->header('Access-Control-Allow-Origin', '*');
            $response->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Accept, Authorization, X-URL-PATH');
            $response->header('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, OPTIONS, DELETE');
        }
        return $response;
    }
}