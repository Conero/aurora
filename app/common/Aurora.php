<?php

/**
 * Auther: Joshua Conero
 * Date: 2017/5/11 0011 17:18
 * Email: brximl@163.com
 * Name: 项目系统基本公共函数
 */
namespace app\common;
use think\Db;

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
}