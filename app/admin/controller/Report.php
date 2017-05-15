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
            $count = $report->count();
            $data = $report->field('descrip,concat(left(content,10),\'***\') as content,is_private,type,mtime')->page($bstp->page_decode(),30)->select();
            $tbody = '';$i = 1;
            foreach ($data as $v){
                $tbody .= '<tr><td>'.$i.'</td><td>'.$v['descrip'].'</td><td>'.$v['content'].'</td><td>'.$v['is_private'].'</td><td>'.$v['type'].'</td><td>'.$v['mtime'].'</td></tr>';
                $i += 1;
            }
            if($tbody){
                $view->assign('tbody',$tbody);
                $view->assign('pageBar',$bstp->pageBar($count));
            }
        });
    }
}