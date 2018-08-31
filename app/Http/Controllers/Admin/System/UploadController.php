<?php
/**
 * Description  CmsTop MediaCloud
 *
 * @Author      liujing <liujing@cmstop.com>
 * @DateTime    2018/8/31 18:01
 * @CopyRight   Beijing CmsTop Technology Co.,Ltd.
 */

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function index(Request $request)
    {
        $year = date('Y');
        $month = date('m');
        $day = date('d');
        $dir = sprintf("upload/%s/%s/%s", $year, $month, $day);
        $path = $request->file('file')->store($dir);
        return compact('path');
    }
}