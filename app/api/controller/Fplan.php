<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/6/22 0022 21:26
 * Email: brximl@163.com
 * Name:
 */

namespace app\api\controller;


use app\common\controller\Api;
use app\common\model\Fnc2000c;
use hyang\Util;
use think\Db;

class Fplan extends Api
{
    // 数据保存
    public function save(){
        $check = $this->needLoginNet($uid);
        if($check) return $check;
        list($data,$mode,$map) = $this->_getSaveData('plan_no');
        $data = Util::dataClear($data,['start_date','end_date','cycle','pid','standard_money']);
        if(!isset($data['cycle']) && isset($data['cycle_unit'])) unset($data['cycle_unit']);
        $fnc = new Fnc2000c();
        Db::startTrans();
        try{
            if($mode == 'A') {
                $data['uid'] = $uid;
                $data['plan_no'] = $fnc->getNoVal();
                if($fnc->save($data)){
                    Db::commit();
                    return $this->FeekMsg('财务计划新增成功!',1);
                }
                return $this->FeekMsg('财务计划新增失败了！');
            }elseif ($mode == 'M'){
                if(!empty($map) && $fnc->save($data,$map)){
                    Db::commit();
                    return $this->FeekMsg('财务计划更新成功!',1);
                }
                return $this->FeekMsg('财务计划更新失败了！');
            }elseif ($mode == 'D'){
                if($this->pushRptBack($fnc->getTable(),$map,'auto')){
                    Db::commit();
                    return $this->FeekMsg('财务计划删除成功!',1);
                }
                return $this->FeekMsg('财务计划删除失败了！');
            }
        }catch (\Exception $e){
            debugOut($e->getMessage()."\r\n".$e->getTraceAsString());
            Db::rollback();
            return $this->FeekMsg('系统出错!');
        }
        //debugOut($data);
        return $this->FeekMsg('系统请求无效!');
    }
}