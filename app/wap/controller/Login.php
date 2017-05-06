<?php
/**
 * User: Joshua Conero
 * Date: 2017/5/6 0006 23:28
 * Email: brximl@163.com
 * Name: 用户登录
 */

namespace app\wap\controller;
use app\common\controller\Wap;
class Login extends Wap
{
    public function index(){
        return $this->fetch();
    }
}