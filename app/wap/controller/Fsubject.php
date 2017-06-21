<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/6/21 0021 21:27
 * Email: brximl@163.com
 * Name:
 */

namespace app\wap\controller;


use app\common\controller\Wap;
use app\common\model\Fnc0030c;

class Fsubject extends Wap
{
    public function index(){
        $this->checkAuth();
        $this->loadScript([
            'title' => '科目管理',
            'js'    => ['/lib/zepto/touch','fsubject/index']
        ]);
        $fnc = new Fnc0030c();
        $page = [];
        $uid = $this->getUserInfo('uid');
        $data = $fnc
            ->where(['private_mk'=>'Y','uid'=>$uid])
            ->whereOr(['private_mk'=>'N'])
            ->limit(20)
            ->select();
        $list = '';
        foreach ($data as $v){
            $list .= '
            <a class="weui-cell weui-cell_access" href="javascript:;">
                <div class="weui-cell__bd">
                    <p>'.$v['subject'].'</p>
                </div>
                <div class="weui-cell__ft"></div>
            </a>
            ';
        }
        if($list) $page['dataList'] = $list;
        if($page) $this->assign('page',$page);
        return $this->fetch();
    }
}