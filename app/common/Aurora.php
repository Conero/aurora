<?php

/**
 * Auther: Joshua Conero
 * Date: 2017/5/11 0011 17:18
 * Email: brximl@163.com
 * Name: 项目系统基本公共函数
 */
namespace app\common;
use hyang\Location;
use hyang\Net;
use think\Config;
use think\Db;
use think\Session;

class Aurora
{
    /**
     * 获取系统反馈的统计值
     * @param $counter
     * @param bool $isGroup
     * @return array|mixed
     */
    public static function getFeekCount($counter,$isGroup=false){
        if($isGroup){
            $data = Db::table('sys_counter')->field('counter,count_start')->where('group_mk', $counter)->select();
            $retVal = [];
            foreach ($data as $v) {
                $key = $v['counter'];
                $retVal[$key] = $v['count_start'];
            }
            return $retVal;
        }else
            return Db::table('sys_counter')->where('counter', $counter)->value('count_start');
    }
//    public function session_cache(){
//        $ip = request()->ip();
//        $CacheFile = hash($ip.rand(100000,999999));
//    }

    /**
     * 访问 session 数据更新
     * @param $key
     * @param null $value
     * @return bool|string|array
     */
    public static function visitSession($key=null,$value=null){
        $skey = Config::get('setting.session_visit_key');
        $isUpdate = false;
        if(Session::has($skey)){
            $data = bsjson(Session::get($skey));
            if(empty($key)) return $data;
            if($value){
                $data[$key] = $value;
                $isUpdate = true;
            }
            elseif (is_array($key)){
                $data = array_merge($data,$key);
                $isUpdate = true;
            }
            elseif ($key){
                return isset($data[$key])? $data[$key]:'';
            }
            if($isUpdate) Session::set($skey,bsjson($data));
        }
        return $isUpdate;
    }
    /**
     * 获取地址信息
     * @return array|mixed
     */
    public static function location(){
        $ip = Net::getNetIp();
        Location::setIp($ip);
        $data = Location::getLocation();
        if(!is_array($data)) $data = [];
        return $data;
    }
}