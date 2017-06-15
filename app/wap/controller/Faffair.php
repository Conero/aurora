<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/6/13 0013 22:02
 * Email: brximl@163.com
 * Name: 事务甲乙方
 */

namespace app\wap\controller;


use app\common\controller\Wap;
use app\common\model\Fnc0020c;
use hyang\Bootstrap;

class Faffair extends Wap
{
    public function index(){
        $this->checkAuth();
        $uid = $this->getUserInfo('uid');
        $fnc20 = new Fnc0020c();
        $data = $fnc20->where('uid',$uid)
            ->order('mtime')
            ->select();
        $listCtt = '';
        foreach ($data as $v){
            $listCtt .= '
            <a class="weui-cell weui-cell_access" href="'.url('faffair/edit','uid='.$v['listid']).'">
                <div class="weui-cell__bd">
                    <p>'.$v['name'].'</p>
                </div>
                <div class="weui-cell__ft"></div>
            </a>
            ';
        }
        if($listCtt) $this->assign('listCtt',$listCtt);
        return $this->fetch();
    }
    public function edit(){
        $this->checkAuth();
        $this->loadScript([
            'js' => 'faffair/edit'
        ]);
        $listid = request()->param('uid');
        $uid = $this->getUserInfo('uid');
        if($listid){
            $fnc20x = new Fnc0020c();
            $data = $fnc20x->get($listid)->toArray();
            if($data['uid'] != $uid){
                $this->getErrorUrl('请求参数有误!');
            }
            $this->assign('data',$data);
            $this->assign('pk_form',Bootstrap::formPkGrid($data));
        }
        return $this->fetch();
    }
}