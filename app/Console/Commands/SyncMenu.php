<?php
/**
 * Description  CmsTop MediaCloud
 *
 * @Author      liujing <liujing@cmstop.com>
 * @DateTime    2018/9/4 19:04
 * @CopyRight   Beijing CmsTop Technology Co.,Ltd.
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncMenu extends Command
{
    protected $signature = 'sync:menu';
    protected $description = '同步微信菜单';


    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 命令执行
     *
     * @return mixed
     */
    public function handle()
    {
        $app = app('wechat.official_account');
        $buttons = [
            [
                "type" => "view",
                "name" => "京选",
                "url"  => "http://wx.jhz.bjue.cn/#/home/index"
            ],
            [
                "type" => "view",
                "name" => "找货",
                "url"  => "http://wx.jhz.bjue.cn/#/search/index"
            ],
            [
                "type" => "view",
                "name" => "我的",
                "url"  => "http://wx.jhz.bjue.cn/#/user/index"
            ]
        ];
        $app->menu->create($buttons);
        return 'success';
    }
}