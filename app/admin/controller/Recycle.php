<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/15 0015 23:32
 * Email: brximl@163.com
 * Name:
 */

namespace app\admin\controller;


use app\common\controller\Web;
use app\common\traits\Admin;

class Recycle extends Web
{
    use Admin;
    public function index(){
        return $this->pageTpl(function ($view){});
    }
}