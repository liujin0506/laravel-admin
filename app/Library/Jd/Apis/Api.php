<?php
/**
 * Description  CmsTop MediaCloud
 *
 * @Author      liujing <liujing@cmstop.com>
 * @DateTime    2018/8/30 10:44
 * @CopyRight   Beijing CmsTop Technology Co.,Ltd.
 */

namespace App\Library\Jd\Apis;

 interface Api
{
     public function getApiMethodName();

     public function getApiParas();

     public function check();

     public function putOtherTextParam($key, $value);

}