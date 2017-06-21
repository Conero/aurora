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
use app\common\model\Fnc0030c;
use app\common\model\Fnc1000c;
use hyang\Bootstrap;

class Faccount extends Wap
{
    // 首页
    public function index(){
        $this->checkAuth();
        $this->loadScript([
            'title' => '财务账单',
            'js'    => ['/lib/zepto/touch','faccount/index']
        ]);
        $uid = $this->getUserInfo('uid');
        // 数据展示方式可选： 按条数/天数/月数/年等显示
        $fnc = new Fnc1000c();
        $data = $fnc
            ->where('uid',$uid)
            ->select()
            ;
        $dataList = '';
        foreach ($data as $v){
            $dataList .= '
            <a class="weui-cell weui-cell_access" href="javascript:;">
                <div class="weui-cell__bd">
                    <p><i class="fa '.($v['type'] == 'IN'? 'fa-plus-circle text-success':'fa-minus-circle text-info').'"></i> '.$v['date'].'/'.$v['money'].'</p>
                </div>
                <div class="weui-cell__ft">'.$v['mtime'].'</div>
            </a>
            ';
        }
        $dataList = $dataList? $dataList:'
            <div class="weui-cell weui-cell_access">
                <div class="weui-cell__bd"><i class="fa fa-warning text-danger"></i></div>
                <div class="weui-cell__ft">
                    您还没一条财务记录
                </div>
            </div>
        ';
        $page = [];
        $page['list'] = $dataList;
        $this->assign('page',$page);
        return $this->fetch();
    }
    // 记账
    public function edit(){
        $this->checkAuth();
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
        $sbjct = new Fnc0030c();
        $data['subject_opts'] = Bootstrap::SelectGrid(function() use ($sbjct){
            $tmpData = $sbjct
                ->field('listid,subject')
                ->limit(10)
                ->select()
                ;
            $retVal = [];
            foreach ($tmpData as $v){
                $retVal[$v['listid']] = $v['subject'];
            }
            return $retVal;
        });

        //$data['subject_opts'] = Bootstrap::SelectGrid(function(){return ['l'=>'5555'];});
        $this->assign('data',$data);
        return $this->fetch();
    }
}