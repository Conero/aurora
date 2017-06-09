<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/6/10 0010 0:11
 * Email: brximl@163.com
 * Name: 移动个人中心
 */

namespace app\wap\controller;


use app\common\controller\Wap;
use app\common\model\User as UserModel;

class User extends Wap
{
    // 首页
    public function index(){
        $this->checkAuth();
        $use = new UserModel();
        $data = $use->get($this->getUserInfo('uid'))->toArray();
        $this->assign('data',$data);
        return $this->fetch();
    }
}