<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/15 0015 21:29
 * Email: brximl@163.com
 * Name:
 */

namespace app\admin\controller;
use app\common\controller\Web;
use app\common\traits\Admin;
use hyang\Bootstrap;
class Visit extends Web
{
    use Admin;
    public function index()
    {
        return $this->pageTpl(function ($view){
            $bstp = new Bootstrap();
            $visit = model('Visit');
            $data = $visit->field('ip,is_mobile,mtime,dct,annlyse_mk')->order('mtime desc')->page($bstp->page_decode(),30)->select();
            $count = $visit->count();
            $tbody = '';$i = 1;
            foreach ($data as $v){
                $tbody .= '<tr><td>'.$i.'</td><td>'.$v['ip'].'</td><td>'.$v['is_mobile'].
                    '</td><td>'.$v['mtime'].'</td><td>'.$v['dct'].
                    '</td><td>'.(empty($v['annlyse_mk'])? '否':'是').'</td></tr>';
                $i += 1;
            }
            if($tbody){
                $view->assign('tbody',$tbody);
                $view->assign('pageBar',$bstp->pageBar($count));
            }
        });
    }
}