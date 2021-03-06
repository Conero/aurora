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
    protected $page_setting = [];
    protected function init()
    {
        $this->page_setting = $this->getParamFromMenu('menu');
    }
    // 首页
    public function index(){
        return $this->pageTpl(function ($view){
            $setting = $this->page_setting;  // 页面配置项
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
            $view->assign('setting',$setting);
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
            $setting = $this->page_setting; 
            $groupMk = request()->param('group');
            // 数据编辑时
            if($groupMk){
                $data = Db::table('sys_menu')
                    ->field('listid,descrip,url,remark,order,icon')
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
                        $icon = $v['icon'];
                        $detail .= '
                            <tr data-id="'.base64_encode($v['listid']).'"><td data-no="'.$i.'">'.$v['order'].'</td>
                                <td><input type="text" name="descrip" class="form-control" value="'.$v['descrip'].'" required></td>
                                <td><input type="text" name="url" class="form-control" value="'.$v['url'].'" required></td>
                                <td>
                                    <div class="input-group">
                                      <span class="input-group-addon">'.
                            ($icon? (substr_count($icon,'/')>0? '<img src="'.$icon.'">':'<i class="'.$icon.'"></i>'):'').'</span>
                                      <input type="text" name="icon" class="form-control" value="'.$icon.'" required></td>
                                    </div>
                                </td>
                                <!--<td><input type="text" name="icon" class="form-control" value="'.$v['icon'].'" required></td>-->
                                <td><input type="text" name="remark" class="form-control" value="'.$v['remark'].'"></td>
                            </tr>
                        ';
                        $i++;
                    }
                    $view->assign('detail',$detail);
                    $view->assign('data',$outline);
                }
            }
            $view->assign('setting',$setting);
        });
    }
    /**
     * 数据保存 2017年5月19日 星期五
     */
    public function save(){
        $data = $this->_getDtlData();
        $addCtt = 0;$delCtt = 0; $editCtt = 0;$order = 1;
        foreach ($data['dtl'] as $value){
            $pk = isset($value['pk'])? base64_decode($value['pk']):null;
            unset($value['pk']);
            if($pk){ // 修改/删除
                if(isset($value['type']) && 'D' == $value['type']){
                    $this->pushRptBack('sys_menu',['listid'=>$pk],'auto');
                    $delCtt += 1;
                }
                else{
                    $value['order'] = $order;
                    if(Db::table('sys_menu')->where('listid',$pk)->update(array_merge($data['sumy'],$value)))
                        $editCtt += 1;
                    $order += 1;
                }
            }else{
                $value['order'] = $order;
                Db::table('sys_menu')->insert(array_merge($data['sumy'],$value));
                $addCtt += 1;
                $order += 1;
            }
        }
        $tmpArr = [];
        if($addCtt > 0) $tmpArr[] = '新增数据'.$addCtt.'条';
        if($delCtt > 0) $tmpArr[] = '删除数据'.$delCtt.'条';
        if($editCtt > 0) $tmpArr[] = '修改数据'.$editCtt.'条';
        if($addCtt == $delCtt && $addCtt == $editCtt && $addCtt == 0) $tmpArr[] = '没有保存任何数据，你可能没有做任何修改!';
        $msg = '本次共提交条'.count($data['dtl']).'数据，其中'.implode(',',$tmpArr);
        $this->success($msg);
    }
}