<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/6/6 0006 21:36
 * Email: brximl@163.com
 * Name:
 */

namespace app\wap\controller;


use app\common\controller\Wap;

class Graffiti extends Wap
{
    public function edit(){
        $this->loadScript([
            'title' => '我要涂鸦',
            'js'    => ['graffiti/edit']
        ]);
        return $this->fetch();
    }
}