<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/10 0010 16:55
 * Email: brximl@163.com
 * Name: 错误提示页面
 */

namespace app\index\controller;

use app\common\controller\Web;

class Error extends Web
{
    public function index(){
        $data = request()->param();
        $this->assign('page',$data);
        return $this->fetch();
    }
}