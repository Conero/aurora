<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/9 0009 22:48
 * Email: brximl@163.com
 * Name: 系统反馈
 */

namespace app\wap\controller;
use app\common\Aurora;
use app\common\controller\Wap;
use app\common\model\Report;
use app\common\SCache;

class Feekback extends Wap
{
    // 首页
    public function index()
    {
        $this->loadScript([
            'title' => '系统反馈',
            'js'    => ['feekback/index']
        ]);
        $page = Aurora::getFeekCount('survey',true);
        $report = new Report();
        $data = $report
            ->alias('a')
            ->join('sys_const b','a.type=b.const_key and b.scope = \'5401\'','LEFT')
            ->field('concat(\'item=\',a.listid) as listid,a.descrip,a.mtime,left(a.content,50) as content,b.const_value as type')
            ->where('a.is_private','N')
            ->limit(5)
            ->order('a.mtime desc')
            ->select();
        $page['list'] = $data;
        $this->assign('page',$page);
        return $this->fetch();
    }
    // 编辑页面
    public function edit(){
        $this->loadScript([
            'js' => ['feekback/edit']
        ]);
        $this->_JsVar('feek_type',$this->getSysConst('5401'));
        return $this->fetch();
    }
    // 阅读
    public function read(){
        $item = request()->param('item');
        if($item){
            $report = new Report();
            $page = $report->where('listid',$item)->find();
            $scache = new SCache();
            if($scache->has('wap_feekback_read_ctt',$item) == false){
                $count = $page['read_cout'] + 1;
                $page['read_cout'] = $count;
                $report->save(['read_cout'=>$count],['listid'=>$item]);
                $scache->set('wap_feekback_read_ctt',$item);
            }

            $page['type'] = $this->getSysConst('5401',$page['type']);
            $this->assign('page',$page);
        }
        return $this->fetch();
    }
}