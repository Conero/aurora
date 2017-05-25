<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/24 0024 21:22
 * Email: brximl@163.com
 * Name:
 */

namespace app\admin\controller;


use app\common\controller\Web;
use app\common\traits\Admin;
use hyang\Bootstrap;
use think\Db;

class Project extends Web
{
    use Admin;
    protected $page_setting = [];
    protected function init()
    {
        $this->page_setting = $this->getParamFromMenu('project');
    }

    // 首页
    public function index(){
        $this->loadScript([
            'title' => '项目管理'
        ]);
        return $this->pageTpl(function ($view){
            $bstp = new Bootstrap();
            $setting = $this->page_setting;  // 页面配置项
            $view->assign('searchBar',$bstp->searchBar([
                'code'  => '项目代码',
                'name'  => '项目名称',
                'descrip'  => '项目描述',
                'mtime'  => '编辑时间',
                'readme'  => '配置md文件',
                'pid'  => '父项',
                'uid'  => '编辑者'
            ]));
            $where = $bstp->getWhere();
            $count = Db::table('prj1000c')->where($where)->count();
            $tbody = $bstp->tbodyGrid([function($data){
                return '<a href="'.url('project/about','uid='.$data['listid']).'">'.$data['code'].'</a>';
            },'name','descrip','readme','pid','mtime','uid'],function ()use($where,$bstp){
                return Db::table('prj1000c')
                    ->field('code,name,concat(left(descrip,10),\'...\') as descrip,readme,pid,mtime,uid,listid')
                    ->where($where)
                    ->select();
            });
            if($tbody){
                $view->assign('tbody',$tbody);
                $view->assign('pageBar',$bstp->pageBar($count));
            }
            $view->assign('setting',$setting);
        });
    }
    // 编辑页面
    public function edit(){
        return $this->pageTpl(function ($view){
            $setting = $this->page_setting;  // 页面配置项
            $uid = request()->param('uid');
            if($uid) {
                $data = Db::table('prj1000c')->where('listid',$uid)->find();
                $view->assign('data',$data);
                $view->assign('td_pk', Bootstrap::formPkGrid($data));
            }
            $view->assign('setting',$setting);
        });
    }
    // 数据保存页面
    public function save(){
        list($data,$type,$map) = $this->_getSaveData();
        if($type == 'M'){
            if(Db::table('prj1000c')->where($map)->update($data))
                $this->success('数据更新成功!');
            else
                $this->error('数据更新失败!');
        }
        elseif ($type == 'D'){
            $this->pushRptBack('prj1000c',$map,'auto');
            $this->success('数据删除成功!');
        }
        else{
            $data['listid'] = getPkValue('pk_prj1000c__listid');
            if(Db::table('prj1000c')->insert($data))
                $this->success('数据新增成功!');
            else
                $this->error('数据新增失败!');
        }
    }
    // 关于项目
    public function about(){
        $uid = request()->param('uid');
        $data = Db::table('prj1000c')->where('listid',$uid)->find();
        $this->loadScript([
            'title' => $data['name'],
            'js'    => ['project/about']
        ]);
        return $this->pageTpl(function ($view) use($data){
            $setting = $this->page_setting;  // 页面配置项
            $view->assign('setting',$setting);
            $view->assign('data',$data);
        });
        //println($uid);
    }
}