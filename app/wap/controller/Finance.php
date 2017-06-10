<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/6/10 0010 23:37
 * Email: brximl@163.com
 * Name: 财务系统
 */

namespace app\wap\controller;


use app\common\controller\Wap;

class Finance extends Wap
{
    public function index(){
        $this->loadScript([
            'title' => '财务系统'
        ]);
        return $this->fetch();
    }
}