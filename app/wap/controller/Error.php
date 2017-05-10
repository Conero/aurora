<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/10 0010 20:05
 * Email: brximl@163.com
 * Name: 移动版错误页面
 */

namespace app\wap\controller;

use app\common\controller\Wap;
class Error extends Wap
{
    public function index(){
        $data = request()->param();
        $this->assign('page',$data);
        return $this->fetch();
    }
}