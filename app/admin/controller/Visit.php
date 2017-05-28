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
    protected $page_setting = [];
    protected function init()
    {
        $this->page_setting = $this->getParamFromMenu('visit');
    }

    public function index()
    {
        return $this->pageTpl(function ($view){
            $setting = $this->page_setting;  // 页面配置项s            
            $bstp = new Bootstrap();
            $where = $bstp->getWhere();
            $visit = model('Visit');
            $view->assign('searchBar',$bstp->searchBar([
                'ip'  => 'ip',
                'is_mobile'  => '是否为移动端',
                'mtime'  => '访问时间',
                'dct'  => '累计访问次数',
                'annlyse_mk'  => '分析标识'
            ]));
            $count = $visit->where($where)->count();
            $tbody = $bstp->tbodyGrid(['ip','is_mobile','mtime','dct','annlyse_mk'],function ()use($visit,$where,$bstp){
                return $visit
                    ->where($where)
                    ->field('ip,is_mobile,mtime,dct,ifnull(annlyse_mk,\'N\') as annlyse_mk')
                    ->page($bstp->page_decode(),30)
                    ->order('mtime desc')
                    ->select();
            });
            if($tbody){
                $view->assign('tbody',$tbody);
                $view->assign('pageBar',$bstp->pageBar($count));
            }
            $view->assign('setting',$setting);
        });
    }
}