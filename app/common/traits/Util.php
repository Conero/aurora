<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/10 0010 16:47
 * Email: brximl@163.com
 * Name:
 */

namespace app\common\traits;


trait Util
{
    /**
     * 错误自动重定位
     * @param null $msg
     * @param bool $feekData
     * @return mixed|null|string|void
     */
    public function getErrorUrl($msg=null,$feekData=false){
        $msg = $msg? $msg:'';
        $url = (IS_MOBILE == 'Y')? urlBuild('!wap:error',"?msg=$msg"):urlBuild('!index:error',"?msg=$msg");
        if($feekData) return $url;
        header('location:'.$url);
    }
    public function getHomeUrl($feekData=false){
        $url = (IS_MOBILE == 'Y')? urlBuild('!wap:'):urlBuild('!index:');
        if($feekData) return $url;
        header('location:'.$url);
    }
}