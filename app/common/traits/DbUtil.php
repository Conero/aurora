<?php

/**
 * User: Joshua Conero
 * Date: 2017/5/7 0007 8:35
 * Email: brximl@163.com
 * Name: 全局数据库-脚本
 */
namespace app\common\traits;
use think\Config;
use think\Request;
use think\Session;
use think\Db;

trait DbUtil
{
    /**
     * 获取系统常量,获取可用获取全部的值
     * @param $scope 作用域，可支持点操作  => $scope.$key 自动解析
     * @param $key 常量名称
     * @return array/string
     */
    protected function getSysConst($scope,$key=null){
        if(substr_count($scope,'.')>0 && empty($key)){ // 存在点操作并键值为空自动解析值
            $index = strpos($scope,'.');
            $scope = substr($scope,0,$index);
            $key = substr($scope,($index+1));
        }
        // 单个值获取
        if($key){
            $retVal = Db::table('sys_const')->where(['scope'=>$scope,'const_key'=>$key])->value('const_value');
            $retVal = $retVal? $retVal:'';
        }else {
            $data = Db::table('sys_const')->where('scope', $scope)->select();
            $retVal = [];
            foreach ($data as $v) {
                $skey = $v['const_key'];
                $svalue = $v['const_value'];
                $retVal[$skey] = $svalue;
            }
        }
        return $retVal;
    }

    /**
     * 获取系统菜单
     * @param $name string
     * @param $mutilate bool 返回多数据
     * @return array
     */
    protected function getSysMenu($name,$mutilate=null){
        $data = Db::table('sys_menu')->where('group_mk',$name)->order('order')->select();
        $retVal = [];
        foreach ($data as $v){
            if($mutilate){
                $retVal[$v['url']] = [
                    'text' => $v['descrip'],
                    'icon' => $v['icon']
                ];
            }
            else $retVal[$v['url']] = $v['descrip'];
        }
        return $retVal;
    }
    /**
     * 访问站点自动登记,获取获取session值
     * @param $UpdateCtt 是否更记录
     */
    protected function autoRecordVisitRecord($UpdateCtt=true){
        sysVisitInfo($UpdateCtt);
    }
    // 数据删除时将数据写到数据回收表
    // 支持多数据 - 2017年2月9日 星期四
    // $autoDelete 自动删除数据
    protected function pushRptBack($table,$data=null,$mkQuery=false){
        try{
            $uid = getUserInfo('uid');
            $request = Request::instance();
            $savedata = [
                'table_name'    => $table,
                'ip'        => $request->ip(),
                'url'       => $request->url()
            ];
            if($uid) $savedata['uid'] = $uid;
            $map = (is_string($mkQuery) && strtolower($mkQuery) == 'auto')? $data:null;
            if($mkQuery){
                $qData = Db::table($table)->where($data)->select();
                if(empty($data)) return false;
                $ctt = 0;
                foreach($qData as $v){
                    $savedata['col_data'] = bsjson($v);
                    if(Db::table('sys_recycle')->insert($savedata)) $ctt += 1;
                }
                // 自动删除数据
                if($map) return Db::table($table)->where($map)->delete();
                return $ctt>0? true:false;
            }
            else{
                $savedata['col_data'] = bsjson($data);
                return Db::table('sys_recycle')->insert($savedata);
            }
        }catch(\Exception $e){
            debugOut($e->getTraceAsString());
        }
        return false;
    }
}