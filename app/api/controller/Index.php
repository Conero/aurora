<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/11 0011 21:59
 * Email: brximl@163.com
 * Name:
 */

namespace app\api\controller;


use app\common\controller\Api;
use app\common\model\Visit;

class Index extends Api
{
    /**
     * 获取访问统计量
     * @return \think\response\Json
     */
    public function visit_count(){
        $data = (new Visit())->getVisitCountData();
        return json($data);
    }
}