<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/9 0009 22:48
 * Email: brximl@163.com
 * Name: 系统反馈
 */

namespace app\wap\controller;
use app\common\Aurora;
use app\common\controller\Wap;

class Feekback extends Wap
{
    public function index()
    {
        $this->loadScript([
            'title' => '系统反馈',
            'js'    => ['feekback/index']
        ]);
        $page = Aurora::getFeekCount('survey',true);
        $this->assign('page',$page);
        return $this->fetch();
    }
    public function edit(){
        $this->loadScript([
            'js' => ['feekback/edit']
        ]);
        return $this->fetch();
    }
}