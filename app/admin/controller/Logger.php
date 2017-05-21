<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/16 0016 15:48
 * Email: brximl@163.com
 * Name: 系统日志
 */

namespace app\admin\controller;


use app\common\controller\Web;
use app\common\model\Loger;
use app\common\traits\Admin;
use hyang\Bootstrap;
use think\Db;
use think\Request;

class Logger extends Web
{
    use Admin;
    // 首页
    public function index(){
        $this->loadScript([
            'title'=>'系统日志-Aurora',
            'js'    => 'logger/index'
        ]);
        return $this->pageTpl(function ($view){
            $bstp = new Bootstrap();
            $where = $bstp->getWhere(null,['_col_'=>'a','account'=>'b']);
            $view->assign('searchBar',$bstp->searchBar([
                'loger'=>'日志名称',
                'code'=>'日志代码',
                'belong_mk'=>'所属标识',
                'type'=>'类型',
                'mtime'=>'编辑时间	',
                'account'=>'编辑者'
            ]));
            $logger = new Loger();
            $count = $where? $logger->where($where)->count():$logger->count();
            $tbody = $bstp->tbodyGrid([function($data){
                return '<a href="'.url('logger/edit','uid='.$data['listid']).'">'.$data['loger'].'</a>';
            },'code','belong_mk','type','mtime','account',function($data){
                return '
                <a href="'.url('logger/msg','uid='.$data['listid']).'">详情</a>
                <a href="javascript:void(0);" data-id="'.base64_encode($data['listid']).'" class="js__del_lnk">
                    <i class="fa fa-trash-o"></i> 删除
                </a>
                ';
            }],
                function () use($bstp,$where,$logger){
                    $data = $logger->alias('a')
                        ->join('sys_user b','a.uid=b.uid','LEFT')
                        ->where($where)
                        ->page($bstp->page_decode(),30)
                        ->order('a.mtime')
                        ->select();
                    return $data;
                });
            if($tbody){
                $view->assign('tbody',$tbody);
                $view->assign('pageBar',$bstp->pageBar($count));
            }
        });
    }
    // 编辑
    public function edit(){
        return $this->pageTpl(function ($view){
            $uid = request()->param('uid');$type = null;
            if($uid){
                $logger = new Loger();
                $data = $logger->get($uid)->toArray();
                $type = $data['type'];
                $view->assign('data',$data);
                $view->assign('td_pk',Bootstrap::formPkGrid($data));
            }
            $view->assign('select_type',Bootstrap::SelectGrid($this->getSysConst('5403'),$type));
        });
    }
    // sfile 系统文件日志
    public function sfile(){
        // 文件删除
        // ?? 删除写入到日志
        $request = Request::instance();
        $type = $request->param('type');
        if($type){
            $logger = new Loger();
            $msg = '';
            if($type == 'fd'){
                $file = $request->param('file');
                if(unlink(ROOT_PATH.'/'.$file)){
                    $logger->write('log_sfile',$file.' 日志文件本删除成功！');
                    urlBuild('.logger/sfile','?dir='.$request->param('dir'));
                }
                $msg = $file.' 文件删除失败!';
            }
            elseif ($type == '2d'){
                $dir = $request->param('dir');
                if(rmdir(ROOT_PATH.'/'.$dir)){
                    $logger->write('log_sfile',$dir.' 日志目录删除成功！');
                    $dir = substr($dir,0,strrpos($dir,'/'));
                    urlBuild('.logger/sfile','?dir='.$dir);
                }
                $msg = $dir.' 日志目录删除失败!';
            }
            if($msg) $logger->write('log_sfile',$msg);
        }
        $this->loadScript([
            'js' => ['/jstree/jstree.min','logger/sfile'],
            'css'=> ['/jstree/themes/default/style.min']
        ]);
        return $this->pageTpl(function ($view){});
    }
    // 日志详情
    public function msg(){
        $this->loadScript([
            'js' => ['logger/msg'],
            'title' => '日志详情'
        ]);
        return $this->pageTpl(function ($view){
            $request = Request::instance();
            $uid = $request->param('uid');
            if($uid){
                $logger = Db::table('sys_loger')->where('listid',$uid)->find();
                $bstp = new Bootstrap();
                $count = Db::table('sys_logmsg')
                    ->where('pid',$uid)
                    ->count();
                $data = Db::table('sys_logmsg')
                    ->field('ifnull(msg,content) as msg,listid,pid,name,uid,mtime')
                    ->where('pid',$uid)
                    ->order('mtime desc')
                    ->page($bstp->page_decode(),30)
                    ->select();
                $view->assign('data',$data);
                $view->assign('pageBar',$bstp->pageBar($count));
                $view->assign('logger',$logger);
            }
        });
    }
    /**
     * 文件系统脱后台数据获取 - ajax
     * @return array
     */
    public function sfile_get(){
        $requset = Request::instance();
        $item = $requset->param('item');
        if($item == 'get_content'){
            $type = $requset->param('type');
            if($type == 'file'){
                return file_get_contents(ROOT_PATH.'/'.$requset->param('name'));
            }
        }
        //debugOut($requset->param());
        $dir = $requset->param('dir');
        $retVal = [];
        $retVal[] = ['id'=>'runtime','text'=>($dir? $dir:'runtime'),'parent'=>'#'];
        /*
        $retVal[] = ['id'=>'runtime','text'=>'runtime','parent'=>'#'];
        $retVal[] = ['id'=>'_scache','parent'=>'runtime','text'=>'scache'];
        $retVal[] = ['id'=>'cache','parent'=>'runtime','text'=>'cache'];
        $retVal[] = ['id'=>'log','parent'=>'runtime','text'=>'log'];
        $retVal[] = ['id'=>'temp','parent'=>'runtime','text'=>'temp'];
        */
        $basedir = ROOT_PATH.($dir? $dir.'/':'/runtime/');
        $ignoreArray = ['.','..','.gitignore'];
        foreach (scandir($basedir) as $v){
            if(in_array($v,$ignoreArray)) continue;
            //$type = is_file($basedir.$v)? 'file':'dir';
            $type = is_dir($basedir.$v)? 'dir':'file';
            $retVal[] = ['id'=>$v,'parent'=>'runtime','text'=>$v,'type'=>$type];
        }
        return $retVal;
    }
    // 数据删除，支持 ajax 删除法
    public function del(){
        list($item,$data) = $this->_getAjaxData();
        if($item == 'r_l_m'){
            $this->pushRptBack('sys_logmsg',['listid'=>$data['uid']],'auto');
            return json(['code'=>1]);
        }
    }
    public function save(){
        list($data,$type,$map) = $this->_getSaveData();
        //println($data,$type,$map);die;
        $logger = new Loger();
        if($type == 'M'){
            if($logger->update($data,$map))
                $this->success('数据更新成功!');
            else
                $this->error('数据更新失败!');
        }
        elseif ($type == 'D'){
            $this->pushRptBack('sys_loger',$map,true);
            $logger->where($map)->delete();
            $this->success('数据删除成功!');
        }
        else{
            $data['listid'] = getPkValue('pk_sys_loger__listid');
            if($logger->insert($data))
                $this->success('数据新增成功!');
            else
                $this->error('数据新增失败!');
        }
    }
}