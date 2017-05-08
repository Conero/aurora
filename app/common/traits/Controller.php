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
        //  js
        if(isset($opt['js']) && $opt['js']){
            $dir = Config::get('setting.static_pref').request()->module().'/js/';
            $js = is_array($opt['js'])? $opt['js']:array($opt['js']);
            foreach($js as $v){
                $script .= '<script src="'.$dir.$v.'.js"></script>';
            }
        }
        //  css
        if(isset($opt['css']) && $opt['css']){
            $dir = Config::get('setting.static_pref').request()->module().'/css/';
            $js = is_array($opt['css'])? $opt['css']:array($opt['css']);
            foreach($js as $v){
                $script .= '<link rel="stylesheet" href="'.$dir.$v.'.css" />';
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
        $skey = Config::get('setting.session_user_key');
        $data = [];
        if(Session::has($skey)){
            $data = Session::get($skey);
            $data = is_array($data)? $data:[];
            if($key && array_key_exists($key,$data)) return $data[$key];
        }
        return $key? "":$data;
    }
}