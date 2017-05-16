<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/15 0015 22:16
 * Email: brximl@163.com
 * Name: 系统管理
 */

namespace app\common\traits;
use app\common\model\Token;
use app\common\SCache;
use think\Request;
use think\View;

trait Admin
{
    /**
     * 页面模板
     * @param $callback
     * @return mixed
     */
    protected function pageTpl($callback){
        $this->authCheckOut();
        // 视图
        $view = new View();
        call_user_func($callback,$view);
        // 页面渲染
        $this->assign('pageContent',$view->fetch());
        $menu = $this->getSysMenu('admin_tpl');
        $admin = [];
        $xhtml = '';
        $requset = Request::instance();
        $curUrl = strtolower('/'.$requset->module().'/'.$requset->controller());
        foreach ($menu as $k=>$v){
            $xhtml .= '<a href="'.$k.'" class="list-group-item '.(substr_count($k,$curUrl) > 0? 'active':'list-group-item-action').'">'.$v.'</a>';
        }
        if($xhtml) $admin['menu'] = $xhtml;
        $this->assign('admin',$admin);
        return $this->fetch('app/admin/view/admin.html');
    }

    /**
     * 权限控制
     */
    protected function authCheckOut(){
        $model = new Token();
        $token = request()->param('token');
        $scache = new SCache();
        $key = 'admin_auth_t20';
        if($scache->key_exist($key)) return true;
        if($token && $model->TokenIsValid($token,'20')){
            $scache->setKv($key,$token);;
            return true;
        }
        $uid = getUserInfo('uid');
        if(!empty($uid)) return true;
        $this->getErrorUrl('地址请求无效');
    }
}