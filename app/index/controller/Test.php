<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/9 0009 14:18
 * Email: brximl@163.com
 * Name: 测试页面
 */

namespace app\index\controller;
use app\common\controller\Web;

class Test extends Web
{
    public function index(){
        return '这是一个测试界面！';
    }
}