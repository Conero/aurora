<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/15 0015 21:04
 * Email: brximl@163.com
 * Name:
 */

namespace app\admin\controller;


use app\common\controller\Web;
use app\common\traits\Admin;
use think\Db;
class Sconst extends Web
{
    use Admin;
    public function index(){
        return $this->pageTpl(function ($view){
            $data = Db::table('sys_const')->field('scope,scope_desc,mtime,uid')->group('scope')->order('mtime desc,scope')->select();
            $tbody = '';$i = 1;
            foreach ($data as $v){
                $tbody .= '<tr><td>'.$i.'</td><td>'.$v['scope'].'</td><td>'.$v['scope_desc'].'</td><td>'.$v['mtime'].'</td><td>'.$v['uid'].'</td></tr>';
                $i += 1;
            }
            if($tbody){
                $view->assign('tbody',$tbody);
                //$view->assign('pageBar',$bstp->pageBar($count));
            }
        });
    }
}