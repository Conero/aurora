<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/15 0015 23:07
 * Email: brximl@163.com
 * Name:
 */

namespace app\admin\controller;


use app\common\controller\Web;
use app\common\traits\Admin;
use hyang\Bootstrap;

class Token extends Web
{
    use Admin;
    // 首页
    public function index(){
        $this->loadScript([
            'title' => '系统令牌',
            'js'    => 'token/index'
        ]);
        return $this->pageTpl(function($view){
            $bstp = new Bootstrap();
            $view->assign('searchBar',$bstp->searchBar([
                'type'=>'类型',
                'expire_in'=>'有限期',
                'v_ctt'=>'请求数',
                'invalid_mk'=>'无效',
                'mtime'=>'编辑时间	',
                'account'=>'编辑者'
            ]));
            $where = $bstp->getWhere(null,['_col_'=>'a','account'=>'b','type'=>'c.const_value']);
            $token = model('Token');
            $count = $token->alias('a')
                ->join('sys_user b','a.uid=b.uid','LEFT')
                ->join('sys_const c','a.type=c.const_key and c.scope=\'5402\'','LEFT')
                ->where($where)
                ->count();
            $tbody = $bstp->tbodyGrid(['token','type','expire_in','v_ctt','invalid_mk','account','mtime',
                function($data){ // 操作
                    return '
                        <a href="javascript:void(0);" data-id="'.base64_encode($data['listid']).'" class="js__del_lnk">
                            <i class="fa fa-trash-o"></i> 删除
                        </a>
                    ';
            }],function () use($token,$where){
                return $token->alias('a')
                    ->join('sys_user b','a.uid=b.uid','LEFT')
                    ->join('sys_const c','a.type=c.const_key and c.scope=\'5402\'','LEFT')
                    ->field('concat(left(a.token,5),\' ***\',right(a.token,3)) as token,
                    concat(a.type,\' | \',c.const_value) as type,ifnull(a.expire_in,\'长期\') as expire_in,
                    a.listid,a.v_ctt,a.invalid_mk,b.account,a.mtime')
                    ->where($where)
                    ->select();
            });
            if($tbody){
                $view->assign('tbody',$tbody);
                $view->assign('pageBar',$bstp->pageBar($count));
            }
        });
    }
    // 编辑
    public function edit(){
        return $this->pageTpl(function ($view){
            $type = '';
            $view->assign('select_type',Bootstrap::SelectGrid($this->getSysConst('5402'),$type));
        });
    }
    // 数据维护
    public function save(){
        list($data,$type,$map) = $this->_getSaveData();
        $token = model('Token');
        if($type == 'M'){
            if($token->update($data,$map))
                $this->success('数据更新成功!');
            else
                $this->error('数据更新失败!');
        }
        elseif ($type == 'D'){
            $this->pushRptBack('sys_loger',$map,true);
            $token->where($map)->delete();
            $this->success('数据删除成功!');
        }
        else{
            $data['listid'] = getPkValue('pk_sys_loger__listid');
            if($token->insert($data))
                $this->success('数据新增成功!');
            else
                $this->error('数据新增失败!');
        }
    }
}