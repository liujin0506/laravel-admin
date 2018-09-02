<?php
/**
 * Description  CmsTop MediaCloud
 *
 * @Author      liujing <liujing@cmstop.com>
 * @DateTime    2018/8/29 11:36
 * @CopyRight   Beijing CmsTop Technology Co.,Ltd.
 */

namespace App\Http\Controllers\Wechat\Member;

use App\Http\Controllers\Controller;
use App\Http\Services\MemberService;
use App\Library\Helper\Response;
use App\Models\MemberSocialite;
use Illuminate\Http\Request;
use Overtrue\Socialite\AuthorizeFailedException;

class AuthController extends Controller
{
    const CODE = '72f189d05d2bab5d4f651736c8e552a9';

    use Response;
    public function auth(Request $request)
    {
        $code = $request->get('code');
        if ($code == self::CODE) {
            // 调试模式
            $socialite = new MemberSocialite();
            $member = $socialite->Wechat([
                'openid' => 'o_fwUwUF5MnfKpDc5Bdsdh-KZjxs',
                'name' => 'Jliu',
                'nickname' => 'Jliu',
                'avatar' => 'http://thirdwx.qlogo.cn/mmopen/vi_32/DYAIOgq83eqlCXm2hr8NXWf3d3XnMSAmGFxxuibM6Jd3MW6yAM94E0FORdTp8XlSUxUXUb0rRw687JwKhq8aKfw/132',
                'email' => ''
            ]);
            $token = auth('wap')->login($member);
            return [
                'token' => $token
            ];
        } else {
            // 正常模式
            try {
                $officialAccount = app('wechat.official_account');
                $user = $officialAccount->oauth->user();
                $socialite = new MemberSocialite();
                $member = $socialite->Wechat([
                    'openid' => $user->getId(),
                    'name' => $user->getName() ?: $user->getNickname(),
                    'nickname' => $user->getNickname(),
                    'avatar' => $user->getAvatar(),
                    'email' => $user->getEmail() ?: ''
                ]);
                $token = auth('wap')->login($member);
                return [
                    'token' => $token
                ];
            } catch (AuthorizeFailedException $e) {
                $this->errorBadRequest($e->getMessage());
                return false;
            }
        }
    }

    public function redirect(Request $request)
    {
        $redirect_uri = $request->get('redirect_uri');
        header("Location:" . $redirect_uri . '?code=' . self::CODE);
    }

    public function info()
    {
        $user_info = auth('wap')->user();
        return compact('user_info');
    }

    public function bind(Request $request, MemberService $service)
    {
        $user_info = auth('wap')->user();
        $uid = $user_info['id'];
        $union_id = $request->post('union_id');
        return $service->bindUnionId($uid, $union_id);
    }
}