<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/6/13 0013 22:02
 * Email: brximl@163.com
 * Name: 事务甲乙方
 */

namespace app\wap\controller;


use app\common\controller\Wap;

class Faffair extends Wap
{
    public function index(){
        return $this->fetch();
    }
    public function edit(){
        $this->loadScript([
            'js' => 'faffair/edit'
        ]);
        return $this->fetch();
    }
}