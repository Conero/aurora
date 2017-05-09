<?php
/**
 * User: Joshua Conero
 * Date: 2017/5/6 0006 23:40
 * Email: brximl@163.com
 * Name: 用户注册
 */

namespace app\wap\controller;

use app\common\controller\Wap;
use app\common\model\User;
use hyang\Util;
class Register extends Wap
{
    public function index(){
        $this->loadScript([
            'title' => '用户注册',
            'js'    => ['register/index']
        ]);
        return $this->fetch();
    }
}