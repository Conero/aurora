<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/6/21 0021 23:49
 * Email: brximl@163.com
 * Name:
 */

namespace app\api\controller;


use app\common\controller\Api;
use app\common\model\Fnc1000c;
use hyang\Util;

class Faccount extends Api
{
    public function save(){
        $check = $this->needLoginNet($uid);
        if($check) return $check;
        list($data,$mode,$map) = $this->_getSaveData('no');
        $data = Util::dataUnset($data,['master']);
        $fnc = new Fnc1000c();
        if($mode == 'A'){
            $data = Util::dataClear($data,['tag_id','slave_id','subject_id']);
            $data['uid'] = $uid;
            $data['no'] = $fnc->getNoVal();
            if($fnc->save($data)) return $this->FeekMsg('记账成功！',1);
            return $this->FeekMsg('记账失败！');
            //debugOut($data);
        }elseif ($mode == 'M'){
            if($fnc->save($data,$map)) return $this->FeekMsg('账单更新成功！',1);
            return $this->FeekMsg('账单更新失败！');
        }elseif ($mode == 'D'){
            if($this->pushRptBack($fnc->getTable(),$map,'auto')) return $this->FeekMsg('账单删除成功！',1);
            return $this->FeekMsg('账单删除失败！');
        }
        return $this->FeekMsg('系统请求失败！');
    }
}