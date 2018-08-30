<?php
/**
 * Description  CmsTop MediaCloud
 *
 * @Author      liujing <liujing@cmstop.com>
 * @DateTime    2018/5/17 16:38
 * @CopyRight   Beijing CmsTop Technology Co.,Ltd.
 */

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Goods;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SyncGoods extends Command
{

    protected $signature = 'sync:goods';
    protected $description = '同步京东商品信息';


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
        $page = Cache::get('goods:page', 10);
        $model = new Goods();
        if ($model->syncGoods($page)) {
            $next = $page > 1 ? $page - 1 : 1;
            Cache::put('goods:page', $next, 10);
            Log::error('采集成功, page:' . $page);
        } else {
            Log::error('采集成功, page:' . $page);
        }

    }
}