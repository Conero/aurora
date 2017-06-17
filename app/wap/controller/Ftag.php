<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/6/17 0017 8:29
 * Email: brximl@163.com
 * Name: 标签管理
 */

namespace app\wap\controller;


use app\common\controller\Wap;
use app\common\model\Fnc0010c;

class Ftag extends Wap
{
    // 主页
    public function index(){
        $this->loadScript([
            'title' => '标签管理',
            'js'    => ['/lib/zepto/touch','ftag/index']
        ]);
        $tagmd = new Fnc0010c();
        $page = [];
        $uid = $this->getUserInfo('uid');
        $data = $tagmd
            ->where(['private_mk'=>'Y','uid'=>$uid])
            ->whereOr(['private_mk'=>'N'])
            ->limit(20)
            ->select();
        $list = '';
        foreach ($data as $v){
            $list .= '
            <a class="weui-cell weui-cell_access" href="javascript:;">
                <div class="weui-cell__bd">
                    <p>'.$v['tag'].'</p>
                </div>
                <div class="weui-cell__ft"></div>
            </a>
            ';
        }
        if($list) $page['tagList'] = $list;
        if($page) $this->assign('page',$page);
        return $this->fetch();
    }
    // 编辑页
    public function edit(){
        return $this->fetch();
    }
}