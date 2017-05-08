<?php

/**
 * User: Joshua Conero
 * Date: 2017/5/7 0007 8:35
 * Email: brximl@163.com
 * Name: 全局数据库-脚本
 */
namespace app\common\traits;
use think\Config;
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
    public function getSysConst($scope,$key=null){
        if(substr_count($scope,'.')>0 && empty($key)){ // 存在点操作并键值为空自动解析值
            $index = strpos($scope,'.');
            $scope = substr($scope,0,$index);
            $key = substr($scope,($index+1));
        }
        // 单个值获取
        if($key){
            $retVal = Db::table('sys_const')->where(['scope'=>$scope,'const_value'=>$key])->value('const_value');
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
     * 访问站点自动登记,获取获取session值
     */
    public function autoRecordVisitRecord(){
        $skey = Config::get('setting.session_visit_key');
        if(!Session::has($skey)){
            $ctt = (Db::table('sys_visit')->count()) + 1;
            $isMobile = isMobile()? 'Y':'N';
            Db::table('sys_visit')->insert([
                'ip' => request()->ip(),
                'is_mobile' => $isMobile,
                'agent' => isset($_SERVER['HTTP_USER_AGENT'])? $_SERVER['HTTP_USER_AGENT']:'',
                'dct'=> $ctt
            ]);
            if($isMobile == 'Y'){
                $SessionData['mcount'] = $ctt;
//                $SessionData['wcount'] = Db::table('sys_visit')->field(['count(*)'=>'ctt'])->where('is_mobile','N')->value('ctt');
                $SessionData['wcount'] = Db::table('sys_visit')->where('is_mobile','N')->count();
            }else{
                $SessionData['wcount'] = $ctt;
//                $SessionData['mcount'] = Db::table('sys_visit')->field(['count(*)'=>'ctt'])->where('is_mobile','Y')->value('ctt');
                $SessionData['mcount'] = Db::table('sys_visit')->where('is_mobile','Y')->count();
            }
            Session::set($skey,bsjson($SessionData));
            return $SessionData;
        }else{
            return bsjson(Session::get($skey));
        }
    }
}