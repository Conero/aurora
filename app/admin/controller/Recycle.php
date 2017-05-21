<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/15 0015 23:32
 * Email: brximl@163.com
 * Name:
 */

namespace app\admin\controller;


use app\common\controller\Web;
use app\common\traits\Admin;
use hyang\Bootstrap;
use think\Db;

class Recycle extends Web
{
    use Admin;
    public function index(){
        return $this->pageTpl(function ($view){
            $bstp = new Bootstrap();
            $where = $bstp->getWhere(null,['_col_'=>'a','account'=>'b']);
            $view->assign('searchBar',$bstp->searchBar([
                'table_name'=>'数据表名',
                'url'=>'请求地址',
                'ip'=>'ip',
                'mtime'=>'编辑时间	',
                'account'=>'编辑者'
            ]));
            $count = Db::table('sys_recycle')
                ->alias('a')
                ->join('sys_user b','a.uid=b.uid','LEFT')
                -> where($where)
                ->count();
            $tbody = $bstp->tbodyGrid(['table_name',function($data){
                $json = base64_decode($data['col_data']);
                $text = substr($json,0,10).'...'.substr($json,strlen($json)-10);
                return '<a href="javascript:void(0);">'.$text.'</a><input type="hidden" value="'.$json.'">';
            },'url','ip','mtime','account'],function () use($where){
                return Db::table('sys_recycle')
                    ->alias('a')
                    ->field('a.table_name,a.col_data,a.url,a.ip,a.mtime,b.account')
                    ->join('sys_user b','a.uid=b.uid','LEFT')
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