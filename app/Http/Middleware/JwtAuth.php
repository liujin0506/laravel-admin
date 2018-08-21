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
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtAuth extends BaseMiddleware
{
    private $token;

    public function handle(Request $request, Closure $next)
    {
        try {
            $this->authenticate($request);
        } catch (\Exception $e) {
            if ($e->getMessage() == 'Token has expired') {
                try {
                    // 刷新用户的 token
                    $this->token = $this->auth->refresh();
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