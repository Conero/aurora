<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/16 0016 15:50
 * Email: brximl@163.com
 * Name: 系统菜单
 */

namespace app\admin\controller;


use app\common\controller\Web;
use app\common\traits\Admin;
use hyang\Bootstrap;
use think\Db;

class Menu extends Web
{
    use Admin;
    // 首页
    public function index(){
        return $this->pageTpl(function ($view){
            $bstp = new Bootstrap();
            $where = $bstp->getWhere(null,['_col_'=>'a','account'=>'b']);
            $count = !empty($where)?
                Db::table('sys_menu')->alias('a')->join('sys_user b','a.uid=b.uid','LEFT')->where($where)->count():
                Db::table('sys_menu')->count();
            $view->assign('searchBar',$bstp->searchBar([
                'group_mk'=>'分组码',
                'group_desc'=>'分组描述',
                'order'=>'顺序',
                'descrip'=>'描述',
                'url'=>'地址',
                'mtime'=>'编辑时间	',
                'account'=>'编辑者'
            ]));
            $tbody = $bstp->tbodyGrid([function($data){
                return '<a href="'.url('menu/edit','group='.$data['group_mk']).'">'.$data['group_mk'].'</a>';
            },'group_desc','order','descrip','url','mtime','account'],
                function () use($bstp,$where){
                    $data = Db::table('sys_menu')
                        ->alias('a')
                        ->join('sys_user b','a.uid=b.uid','LEFT')
                        ->where($where)
                        ->page($bstp->page_decode(),30)
                        ->order('a.group_mk,a.`order`')
                        ->select();
                    return $data;
            });
            if($tbody){
                $view->assign('tbody',$tbody);
                $view->assign('pageBar',$bstp->pageBar($count));
            }

        });
    }
    /**
     * 编辑页面 2017年5月16日 星期二
     */
    public function edit(){
        $this->loadScript([
            'js' => ['menu/edit']
        ]);
        return $this->pageTpl(function($view){
            $groupMk = request()->param('group');
            // 数据编辑时
            if($groupMk){
                $data = Db::table('sys_menu')
                    ->field('listid,descrip,url,remark,order')
                    ->where('group_mk',$groupMk)
                    ->order('order')
                    ->select();
                if($data){
                    $outline = Db::table('sys_menu')
                        ->field('group_mk,group_desc')
                        ->where('group_mk',$groupMk)
                        ->find();
                    $detail = '';$i = 1;
                    foreach ($data as $v){
                        $detail .= '
                            <tr data-id="'.base64_encode($v['listid']).'"><td data-no="'.$i.'">'.$v['order'].'</td>
                                <td><input type="text" name="descrip" class="form-control" value="'.$v['descrip'].'" required></td>
                                <td><input type="text" name="url" class="form-control" value="'.$v['url'].'" required></td>
                                <td><input type="text" name="remark" class="form-control" value="'.$v['remark'].'"></td>
                            </tr>
                        ';
                        $i++;
                    }
                    $view->assign('detail',$detail);
                    $view->assign('data',$outline);
                }
            }
        });
    }
}