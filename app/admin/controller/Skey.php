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
    public function index(){
        return $this->pageTpl(function($view){
            $bstp = new Bootstrap();
            $count = Db::table('sys_key')->count();
            $data = Db::table('sys_key')->page($bstp->page_decode(),30)->select();
            $tbody = '';$i = 1;
            foreach ($data as $v){
                $tbody .= '<tr><td>'.$i.'</td><td>'.$v['name'].'</td><td>'.$v['pref'].'</td><td>'.$v['type'].'</td><td>'.$v['len'].'</td><td>'.$v['idx'].'</td><td>'.$v['mtime'].'</td></tr>';
                $i += 1;
            }
            if($tbody){
                $view->assign('tbody',$tbody);
                $view->assign('pageBar',$bstp->pageBar($count));
            }
        });
    }
}