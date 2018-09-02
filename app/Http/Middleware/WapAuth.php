<?php
/**
 * 自动刷新登录Token
 *
 * @Author      liujing <liujing@cmstop.com>
 * @DateTime    2018/8/21 11:36
 * @CopyRight   Beijing CmsTop Technology Co.,Ltd.
 */

namespace App\Http\Middleware;

use App\Library\Auth\MemberAuth;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class WapAuth extends BaseMiddleware
{
    private $token;

    public function handle(Request $request, Closure $next)
    {
        dd($this->auth);
        $this->checkForToken($request);
        try {
            // 检测用户的登录状态，如果正常则通过
            if ($this->auth->parseToken()->authenticate()) {
                return $next($request);
            }
            throw new UnauthorizedHttpException('jwt-auth', '未登录');
        } catch (\Exception $e) {
            if ($e->getMessage() == 'Token has expired') {
                try {
                    // 刷新用户的 token
                    $old_token = Auth::guard('wap')->getToken();
                    $this->token = Auth::guard('wap')->refresh($old_token);
                    $uid = $this->auth->manager()->getPayloadFactory()->buildClaimsCollection()->toPlainArray()['sub'];
                    auth('wap')->onceUsingId($uid);
                } catch (\Exception $exception) {
                    throw new UnauthorizedHttpException('', '请重新登陆');
                }
            } else {
                throw new UnauthorizedHttpException('', '请重新登陆' . $e->getMessage());
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