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
use hyang\Bootstrap;
use think\Db;
class Sconst extends Web
{
    use Admin;
    protected $page_setting = [];
    protected function init()
    {
        $this->page_setting = $this->getParamFromMenu('sconst');
    }

    // 首页
    public function index(){
        $this->loadScript([
            'title' => '系统常量',
            'js'    => 'sconst/index'
        ]);
        return $this->pageTpl(function ($view){
            $setting = $this->page_setting;  // 页面配置项            
            $bstp = new Bootstrap();
            $where = $bstp->getWhere(null,['_col_'=>'a','account'=>'b']);
            $view->assign('searchBar',$bstp->searchBar([
                'scope'=>'作用域',
                'scope_desc'=>'描述',
                'mtime'=>'编辑时间	',
                'account'=>'编辑者'
            ]));
            $count = Db::table('sys_const')
                ->alias('a')
                ->join('sys_user b','a.uid=b.uid','LEFT')
                ->where($where)
                ->group('a.scope')
                ->count();
            $tbody = $bstp->tbodyGrid([function($data){
                return '<a href="'.url('sconst/edit','scope='.$data['scope']).'">'.$data['scope'].'</a>';
            },
                'scope_desc','mtime','uid',function($data){
                    return '<a href="javascript:void(0);" data-id="'.$data['scope'].'" class="js__del_lnk"><i class="fa fa-trash-o"></i> 删除</a>';
                }],
                function () use($bstp,$where){
                    $data = Db::table('sys_const')
                        ->alias('a')
                        ->join('sys_user b','a.uid=b.uid','LEFT')
                        ->where($where)
                        ->field('a.scope,a.scope_desc,a.mtime,b.account as uid')
                        ->group('a.scope')
                        ->order('a.mtime desc,a.scope')
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
    // 编辑页
    public function edit(){
        $this->loadScript([
            'js' => 'sconst/edit'
        ]);
        return $this->pageTpl(function ($view){
            $setting = $this->page_setting;  // 页面配置项
            $scope = request()->param('scope');
            if($scope){
                $sumy = Db::table('sys_const')->field('scope,scope_desc')
                    ->where('scope',$scope)->find();
                $dtl = Db::table('sys_const')
                    ->field('listid,const_key,const_value,remark')
                    ->where('scope',$scope)->select();
                $detail = ''; $i=1;
                foreach ($dtl as $v){
                    $detail .= '
                    <tr data-id="'.base64_encode($v['listid']).'"><td data-no="'.$i.'">'.$i.'</td>
                    <td><input type="text" name="const_key" class="form-control" value="'.$v['const_key'].'"></td>
                    <td><input type="text" name="const_value" class="form-control" value="'.$v['const_value'].'"></td>
                    <td><input type="text" name="remark" class="form-control" value="'.$v['remark'].'"></td>
                    </tr>';
                    $i++;
                }
                $view->assign('data',$sumy);
                $view->assign('detail',$detail);
            }
            $view->assign('setting',$setting);
        });
    }
    // 数据保存
    public function save(){
        $data = $this->_getDtlData();
        $addCtt = 0;$delCtt = 0; $editCtt = 0;
        foreach ($data['dtl'] as $value){
            $pk = isset($value['pk'])? base64_decode($value['pk']):null;
            unset($value['pk']);
            if($pk){ // 修改/删除
                if(isset($value['type']) && 'D' == $value['type']){
                    $this->pushRptBack('sys_const',['listid'=>$pk],'auto');
                    $delCtt += 1;
                }
                else{
                    if(Db::table('sys_const')->where('listid',$pk)->update(array_merge($data['sumy'],$value)))
                        $editCtt += 1;
                }
            }else{
                Db::table('sys_const')->insert(array_merge($data['sumy'],$value));
                $addCtt += 1;
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
    // 删除数据
    public function del(){
        $scope = request()->param('scope');
        if($scope){
            $this->pushRptBack('sys_const',['scope'=>$scope],'auto');
            $this->success("数据删除成功!");
        }
    }
}