<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/6/15 0015 23:18
 * Email: brximl@163.com
 * Name: 事务甲乙方API
 */

namespace app\api\controller;


use app\common\controller\Api;
use app\common\model\Fnc0020c;
use hyang\Util;

class Faffair extends Api
{
    public function save(){
        $uid = getUserInfo('uid');
        // 用户未登录时
        if(empty($uid)) return $this->FeekMsg('您的凭证无效!');
        list($data,$mode,$map) = $this->_getSaveData();
        $data = Util::dataClear($data,['start_use']);
        //debugOut([$data,$mode,$map]);
        if($mode == 'A'){
            $data['uid'] = $uid;
            $fnc20c = new Fnc0020c($data);
            if($fnc20c->save()) return $this->FeekMsg('数据新增成功!',1);
            return $this->FeekMsg('数据新增失败!');
        }elseif ($mode == 'M'){
            $fnc20c = new Fnc0020c();
            if($fnc20c->save($data,$map))  return $this->FeekMsg('数据更新成功!',1);
            return  $this->FeekMsg('数据更新失败!');
        }elseif ($mode == 'D'){
            $this->pushRptBack('fnc0020c',$map,'auto');
            return $this->FeekMsg('数据删除成功!');
        }
        return $this->FeekMsg('数据操作失败!');
    }
}