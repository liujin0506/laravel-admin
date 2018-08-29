<?php
/**
 * 自动刷新登录Token
 *
 * @Author      liujing <liujing@cmstop.com>
 * @DateTime    2018/8/21 11:36
 * @CopyRight   Beijing CmsTop Technology Co.,Ltd.
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtAuth extends BaseMiddleware
{
    private $token;

    public function handle(Request $request, Closure $next)
    {
        try {
            Auth::guard('web')->user();
        } catch (\Exception $e) {
            if ($e->getMessage() == 'Token has expired') {
                try {
                    // 刷新用户的 token
                    $old_token = Auth::guard('web')->getToken();
                    $this->token = Auth::guard('web')->refresh($old_token);
                    $uid = $this->auth->manager()->getPayloadFactory()->buildClaimsCollection()->toPlainArray()['sub'];
                    auth('web')->onceUsingId($uid);
                } catch (\Exception $exception) {
                    throw new UnauthorizedHttpException('', '请重新登陆');
                }
            } else {
                throw new UnauthorizedHttpException('', '请重新登陆');
            }
        }

        $response = $next($request);
        if ($this->token) {
            $response->header('Access-Control-Expose-Headers', 'Authorization');
            $response->header('Authorization', $this->token);
        }
        return $response;
    }
}