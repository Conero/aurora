<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/24 0024 21:22
 * Email: brximl@163.com
 * Name:
 */

namespace app\admin\controller;


use app\common\controller\Web;
use app\common\model\Prj1000c;
use app\common\model\Prj1002c;
use app\common\traits\Admin;
use hyang\Bootstrap;

class Project extends Web
{
    use Admin;
    protected $page_setting = [];
    protected function init()
    {
        $this->page_setting = $this->getParamFromMenu('project');
    }

    // 首页
    public function index(){
        $this->loadScript([
            'title' => '项目管理'
        ]);
        return $this->pageTpl(function ($view){
            $bstp = new Bootstrap();
            $setting = $this->page_setting;  // 页面配置项
            $view->assign('searchBar',$bstp->searchBar([
                'code'  => '项目代码',
                'name'  => '项目名称',
                'descrip'  => '项目描述',
                'mtime'  => '编辑时间',
                'readme'  => '配置md文件',
                'pid'  => '父项',
                'uid'  => '编辑者'
            ]));
            $where = $bstp->getWhere();
            $prj = new Prj1000c();
            $count = $prj->where($where)->count();
            $tbody = $bstp->tbodyGrid([function($data){
                return '<a href="'.url('project/about','uid='.$data['listid']).'">'.$data['code'].'</a>';
            },'name','descrip','readme','pid','mtime','uid'],function ()use($where,$bstp,$prj){
                return $prj
                    ->field('code,name,concat(left(descrip,10),\'...\') as descrip,readme,pid,mtime,uid,listid')
                    ->where($where)
                    ->select();
            });
            if($tbody){
                $view->assign('tbody',$tbody);
                $view->assign('pageBar',$bstp->pageBar($count));
            }
            $view->assign('setting',$setting);
        });
    }
    // 编辑页面
    public function edit(){
        $this->loadScript([
            'js' => 'project/edit'
        ]);
        return $this->pageTpl(function ($view){
            $setting = $this->page_setting;  // 页面配置项
            $uid = request()->param('uid');
            if($uid) {
                $prj = new Prj1000c();
                $data = $prj->get($uid)->toArray();
                $view->assign('data',$data);
                $view->assign('td_pk', Bootstrap::formPkGrid($data));
            }
            $view->assign('setting',$setting);
        });
    }
    // 数据保存页面
    public function save(){
        list($data,$type,$map) = $this->_getSaveData();
        if(empty($data['pid'])) unset($data['pid']);
        $prj = new Prj1000c();
        if($type == 'M'){
            if($prj->save($data,$map))
                $this->success('数据更新成功!');
            else
                $this->error('数据更新失败!');
        }
        elseif ($type == 'D'){
            $this->pushRptBack('prj1000c',$map,'auto');
            $this->success('数据删除成功!');
        }
        else{
            $data['listid'] = getPkValue('pk_prj1000c__listid');
            $prj->data($data);
            if($prj->save())
                $this->success('数据新增成功!');
            else
                $this->error('数据新增失败!');
        }
    }
    // 关于项目
    public function about(){
        $uid = request()->param('uid');
        $prj = new Prj1000c();
        $data = $prj->get($uid)->toArray();
        $this->loadScript([
            'title' => $data['name'],
            'js'    => ['project/about']
        ]);
        return $this->pageTpl(function ($view) use($data,$prj){
            $setting = $this->page_setting;  // 页面配置项
            //$prj->get($data['listid']);
            $prjSetting = $prj->Settings()
                ->where('pid',$data['listid'])
                ->order('groupid asc,setting_key')
                ->select()
            ;
            $setList = '';
            foreach ($prjSetting as $v){
                $text = empty($v['short_text'])? (empty($v['long_text'])? '':$v['long_text']) : $v['short_text'];
                $text = $text? nl2br($text):$v['json_text'];
                $setList .= '
                    <li class="media">
                        <span class="d-flex mr-3 text-success" ><i class="fa fa-gg"></i></span>
                        <div class="media-body">
                            <h5 class="mt-0 mb-1">'.
                    (empty($v['groupid'])? '':$v['groupid'].'.').'<a href="javascript:void(0);" class="text-info js__sett_edit" data-no="'.$v['listid'].'" 
                    title="'.$v['remark'].'">'.$v['setting_key']
                    .'</a>
                            <a href="javascript:void(0);" class="text-warning js__sett_del" data-no="'.base64_encode($v['listid']).'"><i class="fa fa-trash"></i></a>
                            </h5>
                            <div class="media-descrip">'.$text.'</div>
                        </div>
                    </li>
                ';
            }
            if($setList) $data['setting_list'] = '<ul class="list-unstyled">'.$setList.'</ul>';
            //debugOut($prjSetting);
            $view->assign('setting',$setting);
            $data['news_url'] = urlBuild('!.project/news','?uid='.bsjson(['pid'=>$data['listid']]));
            $view->assign('data',$data);
        });
        //println($uid);
    }
    // 消息发布
    public function news(){
        $this->loadScript([
            'js' => ['/lib/tinymce/tinymce.min','project/news']
        ]);
        return $this->pageTpl(function ($view){
            $setting = $this->page_setting;
            $param = bsjson(request()->param('uid'));
            $pid = $param['pid'];
            $pdata = (new Prj1000c())->field('code,name')->where('listid',$pid)->find();
            $setting['navbar_about'] = '<a href="'.url('project/about','uid='.$pid).'" title="'.$pdata['name'].'">'.$pdata['code'].'</a>';
            $setting['pid_hidden'] = '<input type="hidden" name="pid" value="'.$pid.'">';
            //println($param);
            $view->assign('setting',$setting);
        });
    }
    // 信息发布后台数据维护
    public function news_save(){
        list($data,$mode,$map) = $this->_getSaveData();
        //println($data,$mode,$map);
        $uid = $this->getUserInfo('uid');
        if($mode == 'A'){
            $data['listid'] = getPkValue('pk_prj1002c__listid');
            if($uid) $data['uid'] = $uid;
            $prj12 = new Prj1002c($data);
            if($prj12->save()) $this->success('数据新增成功');
            else $this->error('数据新增失败');
        }elseif ($mode == 'M'){
            $prj12 = new Prj1002c();
            if($prj12->save($data,$map)) $this->success('数据更新成功!');
            else $this->error('数据更新失败!');
        }elseif ($mode == 'D'){
            $this->pushRptBack('prj1002c',$map,'auto');
            $this->success('数据删除成功!');
        }
    }
}