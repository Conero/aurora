<?php
/**
 * User: Joshua Conero
 * Date: 2017/5/6 0006 23:28
 * Email: brximl@163.com
 * Name: 用户登录
 */

namespace app\wap\controller;
use app\common\controller\Wap;
use app\common\model\User;
use think\Config;
use think\Db;
use think\Session;

class Login extends Wap
{
    public function index(){
        $this->loadScript([
            'title' => '用户登录',
            'js'    => ['login/index']
        ]);
        return $this->fetch();
    }
}