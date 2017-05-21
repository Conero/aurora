<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/15 0015 23:34
 * Email: brximl@163.com
 * Name:
 */

namespace app\admin\controller;


use app\common\controller\Web;
use app\common\traits\Admin;
use hyang\Bootstrap;
class Report extends Web
{
    use Admin;
    public function index(){
        return $this->pageTpl(function ($view){
            $report = model('Report');
            $bstp = new Bootstrap();
            $view->assign('searchBar',$bstp->searchBar([
                'descrip'=>'简介',
                'content'=>'内容',
                'is_private'=>'是否保密',
                'type'=>'类型',
                'mtime'=>'编辑时间	'
            ]));
            $where = $bstp->getWhere(null,['_col_'=>'a','type'=>'b.const_value']);
            $count = $report
                ->alias('a')
                ->join('sys_const b','a.type=b.const_key and b.scope=\'5401\'','left')
                ->where($where)
                ->count();
            $tbody = $bstp->tbodyGrid(['descrip','content','is_private','type','mtime'],function () use($report,$bstp,$where){
                return $report->field('a.descrip,concat(left(a.content,10),\'***\') as content,a.is_private,b.const_value as type,a.mtime')
                    ->alias('a')
                    ->join('sys_const b','a.type=b.const_key and b.scope=\'5401\'','left')
                    ->page($bstp->page_decode(),30)
                    ->where($where)
                    ->select();
            });
            if($tbody){
                $view->assign('tbody',$tbody);
                $view->assign('pageBar',$bstp->pageBar($count));
            }
        });
    }
}