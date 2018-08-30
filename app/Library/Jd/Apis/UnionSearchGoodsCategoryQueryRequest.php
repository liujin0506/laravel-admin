<?php
/**
 * Description  CmsTop MediaCloud
 *
 * @Author      liujing <liujing@cmstop.com>
 * @DateTime    2018/8/30 13:52
 * @CopyRight   Beijing CmsTop Technology Co.,Ltd.
 */

namespace App\Library\Jd\Apis;

class UnionSearchGoodsCategoryQueryRequest implements Api
{
    private $apiParas = array();

    public function getApiMethodName(){
        return "jingdong.union.search.goods.category.query";
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
    private $parentId;

    public function setParentId($parentId){
        $this->parentId = $parentId;
        $this->apiParas["parent_id"] = $parentId;
    }

    public function getParentId(){
        return $this->parentId;
    }

    private $grade;

    public function setGrade($grade){
        $this->grade = $grade;
        $this->apiParas["grade"] = $grade;
    }

    public function getGrade(){
        return $this->grade;
    }
}