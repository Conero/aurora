<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/23 0023 21:27
 * Email: brximl@163.com
 * Name: 系统api
 */

namespace app\admin\controller;


use app\common\controller\Web;
use app\common\model\Apis;
use app\common\traits\Admin;
use hyang\Bootstrap;

class Sapi extends Web
{
    use Admin;
    protected $page_setting = [];
    protected function init()
    {
        $this->page_setting = $this->getParamFromMenu('sapi');
    }

    public function index(){
        $this->loadScript([
            'title' => '系统API | 系统管理-Aurora'
        ]);
        return $this->pageTpl(function ($view){
            $setting = $this->page_setting;  // 页面配置项
            
            $bstp = new Bootstrap();
            $view->assign('searchBar',$bstp->searchBar([
                'url' => '地址',
                'count' => '请求数',
                'mtime' => '创建时间'
            ]));
            $where = $bstp->getWhere(null);
            $api = new Apis();
            $count = $api->where($where)->count();
            $tbody = $bstp->tbodyGrid(['url','count','mtime'],function () use($api,$where,$bstp){
                return $api
                    ->where($where)
                    ->page($bstp->page_decode(),30)
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