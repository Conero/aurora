<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/12 0012 18:40
 * Email: brximl@163.com
 * Name: 公共信息页面
 */

namespace app\wap\controller;


use app\common\controller\Wap;

class Msg extends Wap
{
    /**
     * 成功操作页面，用于操作后的跳转
     * 请求值：{title:'',desc:''}
     */
    public function succs(){
        $page = request()->param();
        if(isset($page['url']) && !empty($page['url'])) $page['url'] = urlBuild('!'.$page['url']);
        $this->assign('page',$page);
        return $this->fetch();
    }
    /**
     * 错误操作页面，用于操作后的跳转
     */
    public function erro(){
        return $this->fetch();
    }
}