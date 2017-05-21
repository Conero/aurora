<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/15 0015 23:24
 * Email: brximl@163.com
 * Name:
 */

namespace app\admin\controller;


use app\common\controller\Web;
use app\common\traits\Admin;
use hyang\Bootstrap;
use think\Db;

class Skey extends Web
{
    use Admin;
    // 首页
    public function index(){
        $this->loadScript([
            'title' => '系统主键生成器 | Aurora',
            'js'    => 'skey/index'
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
            $where = $bstp->getWhere(null,['_col_'=>'a','account'=>'b']);
            $count = Db::table('sys_key')
                ->where($where)
                ->count();
            $tbody = $bstp->tbodyGrid(['name','pref','type','len','idx','mtime',function($data){
                return '
                <a href="'.url('skey/edit','name='.$data['name']).'" class="text-info">
                    <i class="fa fa-pencil-square-o"></i>修改
                </a>
                <a href="javascript:void(0);" data-id="'.base64_encode($data['name']).'" class="js__del_lnk text-warning">
                    <i class="fa fa-trash-o"></i>删除
                </a>
               ';
            }],function () use($where){
                return Db::table('sys_key')
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
            $type = 'M';
            $name = request()->param('name');
            $mode = 'A';
            if($name){
                $data = Db::table('sys_key')
                    ->where('name',$name)
                    ->find();
                $type = $data['type'];
                $mode = 'M';
                $view->assign('data',$data);
            }
            $view->assign('mode',$mode);
            $this->_JsVar('mode',$mode);
            $view->assign('select_type',Bootstrap::SelectGrid($this->getSysConst('5404'),$type));
        });
    }
    // 数据保存
    public function save(){
        $this->saveConfig = [
            'raw_pk' => true
        ];
        list($data,$type,$map) = $this->_getSaveData('name');
        //println($data,$type,$map);die;
        if($type == 'M'){
            if(Db::table('sys_key')->where($map)->update($data))
                $this->success('数据更新成功!');
            else
                $this->error('数据更新失败!');
        }
        elseif ($type == 'D'){
            $map['name'] = base64_decode($map['name']); // 与 saveConfig 参数冲突
            $this->pushRptBack('sys_key',$map,'auto');
            $this->success('数据删除成功!');
        }
        else{
            if(Db::table('sys_key')->insert($data))
                $this->success('数据新增成功!');
            else
                $this->error('数据新增失败!');
        }
    }
}