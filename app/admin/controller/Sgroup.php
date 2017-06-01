<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/6/1 0001 21:13
 * Email: brximl@163.com
 * Name: 系统分组
 */

namespace app\admin\controller;


use app\common\controller\Web;
use app\common\model\Group;
use app\common\traits\Admin;
use think\Config;

class Sgroup extends Web
{
    use Admin;
    protected $page_setting = [];
    protected function init()
    {
        $this->page_setting = $this->getParamFromMenu('sgroup');
    }

    public function index(){
        $type = request()->param('type');
        $this->loadScript([
            'title' => '系统分组',
            'js'    => [
                '/lib/gojs/'.(Config::get('app_debug')? 'go-debug':'go'),
                '/lib/jstree/jstree.min',
                'sgroup/index'
            ],
            'css'=> ['/lib/jstree/themes/default/style.min']
        ]);
        return $this->pageTpl(function ($view){
            $setting = $this->page_setting;  // 页面配置项
            $view->assign('setting',$setting);
            //println((new Group())->GoJsTreeNode());
            $this->_JsVar('node',(new Group())->GoJsTreeNode());
            $this->_JsVar('jstree',(new Group())->JsTreeData());
        });
    }
}