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
            if($v['type'] == 'M0') $icon = '<i class="fa fa-user text-success"></i> ';
            elseif ($v['type'] == 'S0') $icon = '<i class="fa fa-info-circle text-info"></i> ';
            else $icon = '<i class="fa fa-question text-primary"></i> ';
            $listCtt .= '
            <a class="weui-cell weui-cell_access" href="'.url('faffair/edit','uid='.$v['listid']).'">
                <div class="weui-cell__bd">
                    <p>'.$icon.$v['name'].'</p>
                </div>
                <div class="weui-cell__ft"></div>
            </a>
            ';
        }
        if($listCtt) $this->assign('listCtt',$listCtt);
        return $this->fetch();
    }
    // 编辑页面
    public function edit(){
        $this->checkAuth();
        $this->loadScript([
            'js' => 'faffair/edit'
        ]);
        $listid = request()->param('uid');
        $uid = $this->getUserInfo('uid');
        $fnc20x = new Fnc0020c();
        $select = null;$checkd=null;
        if($listid){
            $data = $fnc20x->get($listid)->toArray();
            if($data['uid'] != $uid){
                $this->getErrorUrl('请求参数有误!');
            }
            $this->assign('data',$data);
            $select = $data['type'];
            if($data['use_mk'] == 'N') $checkd = true;
            $this->assign('pk_form',Bootstrap::formPkGrid($data));
        }
        // 选取最近设置的十个
        $qdata = $fnc20x->where('uid',$uid)
            ->where('group_mk','not null')
            ->field('group_mk')
            ->order('mtime desc')
            ->group('group_mk')
            ->limit(10)
            ->select();
        $groups = [];
        foreach ($qdata as $v){
            $groups[] = $v['group_mk'];
        }
        $page = [];
        if($groups){
            $this->_JsVar('groups',$groups);
            $page['group_sel_able'] = 'Y';
        }
        $typeSel = Bootstrap::SelectGrid([
            'M0' => '事务甲方',
            'S0' => '事务乙方',
            '00' => '未区分',
        ],$select);
        // <input id="usemk_ipter" class="weui-switch-cp__input" name="use_mk" value="Y" type="checkbox" checked="checked">
        $page['use_mk'] = Bootstrap::RadioGrid('id="usemk_ipter" class="weui-switch-cp__input" name="use_mk" value="N"',$checkd);
        $page['typeSel'] = $typeSel;
        // 辅助操作
        $this->assign('page',$page);
        return $this->fetch();
    }
}