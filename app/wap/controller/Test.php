<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/9 0009 14:22
 * Email: brximl@163.com
 * Name: 移动端测试页面
 */
namespace app\wap\controller;

use app\common\controller\Wap;
header('Content-Type: text/html; charset=utf-8');
// 导入微信开发包

class Test extends Wap
{
    public function index()
    {
        return '移动端测试界面';
    }
    // 主键测试
    public function newsn(){
        $time = time();
        $date = date('Y-m-d H:i:s',$time);
        println(strtotime($date),$date,$time);
        //println(getPkValue(null));
    }
}