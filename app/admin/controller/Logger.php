<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/16 0016 15:48
 * Email: brximl@163.com
 * Name: 系统日志
 */

namespace app\admin\controller;


use app\common\controller\Web;
use app\common\traits\Admin;

class Logger extends Web
{
    use Admin;
    public function index(){
        return $this->pageTpl(function (){});
    }
}