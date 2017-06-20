<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/6/10 0010 23:46
 * Email: brximl@163.com
 * Name: 财务账单
 */

namespace app\wap\controller;


use app\common\Aurora;
use app\common\controller\Wap;

class Faccount extends Wap
{
    // 首页
    public function index(){
        $this->loadScript([
            'title' => '财务账单',
            'js'    => ['/lib/zepto/touch','faccount/index']
        ]);
        return $this->fetch();
    }
    // 记账
    public function edit(){
        $this->loadScript([
            'title' => '记账',
            'js'    => ['/lib/zepto/touch','faccount/edit']
        ]);
        $data = ['date'=>date('Y-m-d')];
        $city = Aurora::visitSession('city');
        if(empty($city)){
            $location = Aurora::location();
            if(empty($location['code'])){
                $city = $location['data']['city'];
                Aurora::visitSession('city',$city);
            }
        }
        if(empty($data['city'])) $data['city'] = $city;
        $this->assign('data',$data);
        return $this->fetch();
    }
}