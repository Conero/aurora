<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/7 0007 12:23
 * Email: brximl@163.com
 * Name: 控制器扩展
 */

namespace app\common\traits;

use think\Config;
use think\Session;

trait Controller
{
    use Util;
    //  js/css 第一个 "/" 开头时不自动加载模块名
    //  加载前端工具 2016年9月21日 星期三   {auth: 权限标识,afterAuthFn:function 授权完成以后自定义脚本,title:页面标题,require:利用 FrontBuild 加载脚本, beforeLoadFront:string/function 加载前端脚本以前,js:js 脚本,css: css 脚本,afterLoadFront: string/function 加载前端页面以后,more:headplus,bootstrap: true 开启}
    // 回调函数时自动传入 $this 对象
    public function loadScript($opt,$feek=false){
        // 权限检测
        if(isset($opt['auth'])){
            // 权限控制
        }
        //  未加载到 view对象时无效
        if(!method_exists($this,'assign')) return false;
        $AppAssignData = [];
        //  标题
        if(isset($opt['title'])){
            $AppAssignData['title'] = $opt['title'];
        }
        $script = '';
        if(isset($opt['afterAuthFn']) && ($opt['afterAuthFn'] instanceof \Closure)) $script .= call_user_func($opt['afterAuthFn'],$this);

        // 自定义脚本 - 前端载入以前
        if(isset($opt['beforeLoadFront'])){
            $script .= ($opt['beforeLoadFront'] instanceof \Closure)? call_user_func($opt['beforeLoadFront'],$this) : $opt['beforeLoadFront'];
        }
        $Pref = Config::get('setting.static_pref');
        //  js
        if(isset($opt['js']) && $opt['js']){
            $dir = $Pref.request()->module().'/js/';
            $js = is_array($opt['js'])? $opt['js']:array($opt['js']);
            foreach($js as $v){
                if(strpos($v,'/') === 0) $jsSrc = $Pref.substr($v,1);
                else $jsSrc = $dir.$v;
                $script .= '<script src="'.$jsSrc.'.js"></script>';
            }
        }
        //  css
        if(isset($opt['css']) && $opt['css']){
            $dir = $Pref.request()->module().'/css/';
            $js = is_array($opt['css'])? $opt['css']:array($opt['css']);
            foreach($js as $v){
                if(strpos($v,'/') === 0) $cssHref = $Pref.substr($v,1);
                else $cssHref = $dir.$v;
                $script .= '<link rel="stylesheet" href="'.$cssHref.'.css" />';
            }
        }
        // 自定义脚本 - 前端载入以后
        if(isset($opt['afterLoadFront'])){
            $script .= ($opt['afterLoadFront'] instanceof \Closure)? call_user_func($opt['afterLoadFront'],$this) : $opt['afterLoadFront'];
        }
        if($script) $AppAssignData['web_front'] = $script;
        if($feek) return $AppAssignData;//生成 HTML
        $this->assign('app',$AppAssignData);
    }
    /**
     * 获取用户信息数据
     * @param null $key
     * @return array|mixed|string
     */
    public function getUserInfo($key=null){
        return getUserInfo($key);
    }
}