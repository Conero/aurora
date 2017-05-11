<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/11 0011 15:35
 * Email: brximl@163.com
 * Name: 系统反馈
 */

namespace app\api\controller;


use app\common\controller\Api;
use think\Db;

class Feek extends Api
{
    /**
     * 调查信息
     * param: type * 类型 ; data * 相关数据
     */
    public function survey(){
        $search = request()->param();
        $type = isset($search['type'])? $search['type']:'';
        $msg = "接口请求无效";
        if($type == 'support_or_not'){ // 对系统标识支持函数喜欢
            $qData = Db::query('select get_counter(?,?,?) as `counter`',[$search['data'],'survey','conero']);
            $msg = $qData[0]['counter'];
            if($msg == -1) $msg = '系统出错，请向网站报告！';
            else return $this->FeekMsg($msg,1);
        }
        return $this->FeekMsg($msg);
    }
}