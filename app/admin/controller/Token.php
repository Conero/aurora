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
    public function index(){
        return $this->pageTpl(function($view){
            $token = model('Token');
            $tbody = '';$i=1;
            $bstp = new Bootstrap();
            $count = $token->count();
            $data = $token->field('concat(left(token,5),\'***\',right(token,3)) as token,type,expire_in,v_ctt,invalid_mk,uid,mtime')->page($bstp->page_decode(),30)->select();
            foreach ($data as $v){
                $tbody .= '<tr><td>'.$i.'</td><td>'.$v['token'].'</td><td>'.$v['type'].'</td><td>'.$v['expire_in'].'</td><td>'.$v['v_ctt'].'</td><td>'.$v['invalid_mk'].'</td><td>'.$v['uid'].'</td><td>'.$v['mtime'].'</td></tr>';
                $i += 1;
            }
            if($tbody){
                $view->assign('tbody',$tbody);
                $view->assign('pageBar',$bstp->pageBar($count));
            }
        });
    }
}