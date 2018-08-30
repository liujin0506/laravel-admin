<?php
/**
 * Description  CmsTop MediaCloud
 *
 * @Author      liujing <liujing@cmstop.com>
 * @DateTime    2018/8/30 14:03
 * @CopyRight   Beijing CmsTop Technology Co.,Ltd.
 */

namespace App\Library\Jd\Apis;

class UnionDoubanBookServiceQueryBookUrlListRequest implements Api
{
    private $apiParas = array();

    public function getApiMethodName(){
        return "jingdong.UnionDoubanBookService.queryBookUrlList";
    }

    public function getApiParas(){
        return json_encode($this->apiParas);
    }

    public function check(){

    }

    public function putOtherTextParam($key, $value){
        $this->apiParas[$key] = $value;
        $this->$key = $value;
    }
}