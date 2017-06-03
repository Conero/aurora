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
use app\common\model\Role;
use app\common\traits\Admin;
use hyang\Bootstrap;
use think\Config;

class Sgroup extends Web
{
    use Admin;
    protected $page_setting = [];
    protected function init()
    {
        $this->page_setting = $this->getParamFromMenu('sgroup');
    }
    // 首页
    public function index(){
        //$type = request()->param('type');
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
    // 编辑页面
    public function edit(){
        $this->loadScript([
            'js' => 'sgroup/edit'
        ]);
        return $this->pageTpl(function ($view){
            $setting = $this->page_setting;
            $uid = request()->param('uid');
            // 数据编辑
            if($uid){
                $groupMd = new Group();
                $data = $groupMd
                    ->where('code',$uid)
                    ->find()
                    ->toArray();
                $roleMd = new Role();
                $sumyData = $roleMd
                    ->where('pid',$data['listid'])
                    ->order('code asc')
                    ->select()
                ;
                $sumyList = '';$i = 1;
                foreach ($sumyData as $v){
                    $sumyList .= '
                        <tr data-id="'.base64_encode($v['listid']).'">
                            <td data-no="1">'.$i.'</td>
                            <td><input type="text" name="code" class="form-control" value="'.$v['code'].'" required></td>
                            <td><input type="text" name="descrip" class="form-control" value="'.$v['descrip'].'" required></td>
                            <td><input type="text" name="remark" class="form-control" value="'.$v['remark'].'"></td>
                        </tr>
                    ';
                    $i++;
                }
                $data['sumyList'] = $sumyList;
                $view->assign('data',$data);
                $view->assign('td_pk',Bootstrap::formPkGrid($data));
            }
            $view->assign('setting',$setting);
        });
    }
    // 数据保存
    public function save(){
        $uid = $this->getUserInfo('uid');
        if(empty($uid)) return $this->error('你还没有登录系统，该数据操作已被禁止!');
        $data = $this->_getDtlData();
        list($sumy,$sumyMode,$sumyMap) = $this->_getSaveData(null,$data['sumy']);
        $pid = '';
        if($sumyMode == 'A'){
            $sumy['uid'] = $uid;
            $groupMd = new Group($sumy);
            $groupMd->save();
            $pid = $groupMd->listid;
        }elseif ($sumyMode == 'M'){
            $groupMd = new Group();
            $pid = $sumyMap['listid'];
            $groupMd->save($sumy,$sumyMap);
        }
        if($pid){
            $Actt = 0;$Mctt = 0;$Dctt = 0;
            foreach ($data['dtl'] as $v){
                list($dtl,$mode,$map) = $this->_getSaveData(null,$v);
                if($mode == 'A'){
                    $dtl['uid'] = $uid;
                    $dtl['pid'] = $pid;
                    $roleMd = new Role($dtl);
                    if($roleMd->save()) $Actt += 1;
                }elseif ($mode == 'M'){
                    $roleMd = new Role();
                    if($roleMd->save($dtl,$map)) $Mctt += 1;
                }elseif($mode == 'D'){
                    $this->pushRptBack('sys_role',$mode,'auto');
                    $Dctt += 1;
                }
            }
            $tmpArr = [];
            if($Actt > 0) $tmpArr[] = '新增数据'.$Actt.'条';
            if($Dctt > 0) $tmpArr[] = '删除数据'.$Dctt.'条';
            if($Mctt > 0) $tmpArr[] = '修改数据'.$Mctt.'条';
            if($Actt == $Dctt && $Actt == $Mctt && $Actt == 0) $tmpArr[] = '没有保存任何数据，你可能没有做任何修改!';
            $msg = '本次共提交条'.count($data['dtl']).'数据，其中'.implode(',',$tmpArr);
            $this->success($msg);
        }
        $this->error('数据保存出错！');
    }
}