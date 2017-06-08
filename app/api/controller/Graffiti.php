<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/6/6 0006 22:57
 * Email: brximl@163.com
 * Name:
 */

namespace app\api\controller;


use app\common\controller\Api;
use app\common\model\Graffiti as Graff;
use hyang\Location;
use think\Db;

class Graffiti extends Api
{
    /**
     * 数据保存
     */
    public function save(){
        list($data,$mode,$map) = $this->_getSaveData();
        //debugOut(Location::getLocation());return $this->FeekMsg('测试请求!!');
        //数据后台验证
        Db::startTrans();
        try{
            if($mode == 'A'){
                $uid = getUserInfo('uid');
                $graf = new Graff();
                $data['listid'] = $graf->getPkVal();
                if($uid) $data['uid'] = $uid;
                $ip = request()->ip();
                // 本地ip地址过滤
                if($ip != '127.0.0.1') Location::setIp($ip);
                $location = Location::getLocation();
                if(empty($location['code']) && isset($location['data'])){
                    $rdata = $location['data'];
                    $data['ip'] = $rdata['ip'];
                    $data['city'] = $rdata['city'];
                    $data['address'] = $rdata['city'].'.'.$rdata['country'].'('.$rdata['isp'].')';
                }
                $graf->data($data);
                if($graf->save()){
                    Db::commit();
                    return $this->FeekMsg('数据新增成功!',1);
                }
                return $this->FeekMsg('数据新增失败!');
            }elseif ($mode == 'M'){
                $graf = new Graff();
                if($graf->save($data,$map)){
                    Db::commit();
                    return $this->FeekMsg('数据更新成功!',1);
                }
                return $this->FeekMsg('数据更新失败!');
            }elseif ($mode == 'D'){
                $this->pushRptBack('graffiti',$map,'auto');
                Db::commit();
                return $this->FeekMsg('数据删除成功!',1);
            }

        }catch (\Exception $e){
            debugOut($e->getMessage()."\r\n".$e->getTraceAsString());
            Db::rollback();
            return $this->FeekMsg('数据维护出错!');
        }
        return $this->FeekMsg('非法请求地址!');
    }

    /**
     * 数据获取
     */
    public function get(){
        $graff = new Graff();
        $data = $graff
            //->where('')
            ->limit(30)
            ->order('mtime desc')
            ->select();
        return $this->FeekMsg($data);
    }
}