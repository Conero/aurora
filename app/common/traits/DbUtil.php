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
    public function getSysConst($scope,$key=null){
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
     * @param $name
     * @return array
     */
    public function getSysMenu($name){
        $data = Db::table('sys_menu')->where('group_mk',$name)->order('order')->select();
        $retVal = [];
        foreach ($data as $v){
            $retVal[$v['url']] = $v['descrip'];
        }
        return $retVal;
    }
    /**
     * 访问站点自动登记,获取获取session值
     * @param $UpdateCtt 是否更记录
     */
    public function autoRecordVisitRecord($UpdateCtt=true){
        $skey = Config::get('setting.session_visit_key');
        if(!Session::has($skey)){
            $ctt = (Db::table('sys_visit')->count()) + 1;
            $isMobile = isMobile()? 'Y':'N';
            // 不更新统计量，可用于开发者开发过滤统计或者反爬虫等
            if($UpdateCtt)
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
    // 数据删除时将数据写到数据回收表
    // 支持多数据 - 2017年2月9日 星期四
    // $autoDelete 自动删除数据
    public function pushRptBack($table,$data=null,$mkQuery=false){
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