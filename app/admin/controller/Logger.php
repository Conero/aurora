<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/16 0016 15:48
 * Email: brximl@163.com
 * Name: 系统日志
 */

namespace app\admin\controller;


use app\common\controller\Web;
use app\common\model\Loger;
use app\common\traits\Admin;
use hyang\Bootstrap;
use think\Request;

class Logger extends Web
{
    use Admin;
    // 首页
    public function index(){
        return $this->pageTpl(function ($view){
            $bstp = new Bootstrap();
            $where = $bstp->getWhere(null,['_col_'=>'a','account'=>'b']);
            $view->assign('searchBar',$bstp->searchBar([
                'loger'=>'日志名称',
                'code'=>'日志代码',
                'belong_mk'=>'所属标识',
                'type'=>'类型',
                'mtime'=>'编辑时间	',
                'account'=>'编辑者'
            ]));
            $logger = new Loger();
            $count = $where? $logger->where($where)->count():$logger->count();
            $tbody = $bstp->tbodyGrid([function($data){
                return '<a href="'.url('logger/edit','uid='.$data['listid']).'">'.$data['loger'].'</a>';
            },'code','belong_mk','type','mtime','account',function($data){
                return '<a href="'.url('logger/msg','uid='.$data['listid']).'">详情</a>';
            }],
                function () use($bstp,$where,$logger){
                    $data = $logger->alias('a')
                        ->join('sys_user b','a.uid=b.uid','LEFT')
                        ->where($where)
                        ->page($bstp->page_decode(),30)
                        ->order('a.mtime')
                        ->select();
                    return $data;
                });
            if($tbody){
                $view->assign('tbody',$tbody);
                $view->assign('pageBar',$bstp->pageBar($count));
            }
        });
    }
    // sfile 系统文件日志
    public function sfile(){
        $this->loadScript([
            'js' => ['/jstree/jstree.min','logger/sfile'],
            'css'=> ['/jstree/themes/default/style.min']
        ]);
        return $this->pageTpl(function ($view){});
    }

    /**
     * 文件系统脱后台数据获取 - ajax
     * @return array
     */
    public function sfile_get(){
        $requset = Request::instance();
        $item = $requset->param('item');
        if($item == 'get_content'){
            $type = $requset->param('type');
            if($type == 'file'){
                return file_get_contents($requset->param('name'));
            }
        }
        //debugOut($requset->param());
        $dir = $requset->param('dir');
        $retVal = [];
        $retVal[] = ['id'=>'runtime','text'=>($dir? $dir:'runtime'),'parent'=>'#'];
        /*
        $retVal[] = ['id'=>'runtime','text'=>'runtime','parent'=>'#'];
        $retVal[] = ['id'=>'_scache','parent'=>'runtime','text'=>'scache'];
        $retVal[] = ['id'=>'cache','parent'=>'runtime','text'=>'cache'];
        $retVal[] = ['id'=>'log','parent'=>'runtime','text'=>'log'];
        $retVal[] = ['id'=>'temp','parent'=>'runtime','text'=>'temp'];
        */
        $basedir = ROOT_PATH.($dir? $dir:'/runtime/');
        $ignoreArray = ['.','..','.gitignore'];
        foreach (scandir($basedir) as $v){
            if(in_array($v,$ignoreArray)) continue;
            $type = is_file($basedir.$v)? 'file':'dir';
            $retVal[] = ['id'=>$v,'parent'=>'runtime','text'=>$v,'type'=>$type];
        }
        return $retVal;
    }
}