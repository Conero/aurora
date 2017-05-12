<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/11 0011 15:35
 * Email: brximl@163.com
 * Name: 系统反馈
 */

namespace app\api\controller;


use app\common\controller\Api;
use app\common\model\Report;
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

    /**
     * 系统报告数据提交保存
     * $param:  itemid -> listid(非真实id名称)
     * @return \think\response\Json
     */
    public function report(){
        $data = request()->param();
        if(!captcha_check($data['code'])) return $this->FeekMsg('验证码无效！');
        unset($data['code']);
        $itemId = isset($data['itemid'])? $data['itemid']:'';
        $report = new Report();
        if($itemId){
            $uid = getUserInfo('uid');
            if($uid) $data['uid'] = $data;
            $data['uip'] = request()->ip();
            if($report->where('listid',$itemId)->update($data)) return $this->FeekMsg('数据修改成功!',1);
        }
        else{
            $data['listid'] = getPkValue('pk_sys_report__listid');
            if($report->insert($data)) return $this->FeekMsg('数据提交成功!',1);
        }
        return $this->FeekMsg('数据维护失败!');
    }
}