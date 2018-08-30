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
use Illuminate\Console\Command;

class SyncCategory extends Command
{

    protected $signature = 'sync:category';
    protected $description = '同步京东商品分类信息';


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
        $model = new Category();
        try {
            $data = $model->syncCategory();
            $this->info('同步成功！');
        } catch (\Exception $e) {
            $this->error('同步失败， 失败原因：' . $e->getMessage());
        }
        return true;
    }
}