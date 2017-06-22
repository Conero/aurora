<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/6/22 0022 17:33
 * Email: brximl@163.com
 * Name: 财务计划
 */

namespace app\wap\controller;


use app\common\controller\Wap;
use app\common\model\Fnc2000c;
use hyang\Bootstrap;

class Fplan extends Wap
{
    // 首页
    public function index(){
        $this->checkAuth();
        $this->loadScript([
            'title' => '财务计划'
        ]);
        $uid = $this->getUserInfo('uid');
        // 数据展示方式可选： 按条数/天数/月数/年等显示
        $fnc = new Fnc2000c();
        $data = $fnc
            ->where('uid',$uid)
            ->select()
        ;
        $dataList = '';
        foreach ($data as $v){
            $dataList .= '
            <a class="weui-cell weui-cell_access" href="'.url('fplan/edit','item='.$v['plan_no']).'">
                <div class="weui-cell__bd">
                    <p><i class="fa fa-clock-o'.($v['end_mk'] == 'Y'? '':' text-success').'"></i>  '.$v['plan'].'</p>
                </div>
                <div class="weui-cell__ft">'.$v['mtime'].'</div>
            </a>
            ';
        }
        $dataList = $dataList? $dataList:'
            <div class="weui-cell weui-cell_access">
                <div class="weui-cell__bd"><i class="fa fa-warning text-danger"></i></div>
                <div class="weui-cell__ft">
                    您还没一条财务计划记录
                </div>
            </div>
        ';
        $page = [];
        $page['list'] = $dataList;
        $this->assign('page',$page);
        return $this->fetch();
    }
    // 编辑
    public function edit(){
        $this->loadScript([
            'title' => '编辑 | 财务计划',
            'js'    => ['/lib/zepto/touch','fplan/edit']
        ]);
        $item = request()->param('item');
        if($item){
            $fnc = new Fnc2000c();
            $data = $fnc->get($item)->toArray();
            $this->assign('data',$data);
            $this->assign('f_pk_grid',Bootstrap::formPkGrid($data,'plan_no'));
        }
        return $this->fetch();
    }
}