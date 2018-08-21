<?php
/**
 * Description  CmsTop MediaCloud
 *
 * @Author      liujing <liujing@cmstop.com>
 * @DateTime    2018/8/21 16:14
 * @CopyRight   Beijing CmsTop Technology Co.,Ltd.
 */

namespace App\Http\Controllers\Admin\Cms;


use App\Http\Controllers\Controller;
use App\Http\Services\UserService;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    public function index(Request $request)
    {
        $service = new UserService();
        return $service->index($request->all());
    }

    public function show($id)
    {
        return [
            'status' => 'draft',
            'title' =>  '', // 文章题目
            'content'=> '', // 文章内容
            'content_short' =>   '', // 文章摘要
            'source_uri' =>   '', // 文章外链
            'image_uri' =>   '', // 文章图片
            'display_time' =>   '', // 前台展示时间
            'id' =>   $id,
            'platforms' =>   ['a-platform'],
            'comment_disabled' =>   false,
            'importance' =>   0
        ];
    }
}