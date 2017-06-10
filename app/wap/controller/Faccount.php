<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/6/10 0010 23:46
 * Email: brximl@163.com
 * Name: 财务账单
 */

namespace app\wap\controller;


use app\common\controller\Wap;

class Faccount extends Wap
{
    // 首页
    public function index(){
        return $this->fetch();
    }
    // 记账
    public function edit(){
        return $this->fetch();
    }
}