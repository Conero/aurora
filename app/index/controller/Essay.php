<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/29 0029 22:41
 * Email: brximl@163.com
 * Name: web版本文章广场
 */

namespace app\index\controller;


use app\common\controller\Web;
use hyang\Bootstrap;
use think\Db;
use app\common\SCache;

class Essay extends Web
{
    // 首页
    public function index(){
        $this->loadScript([
            'title' => '文林轩'
        ]);
        $where = ['is_private'=>'N'];
        $filter = request()->get('filter');
        $value = request()->get('value');
        if($value && $filter) $where[$filter] = ['like',"%$value%"];
        $bstp = new Bootstrap();
        $data = Db::table('atc1000c')
            ->where($where)
            ->order('date desc')
            ->page($bstp->page_decode(),30)
            ->select();
        $count = Db::table('atc1000c')
            ->where($where)
            ->count();
        $list = '';
        foreach ($data as $v){
            $list .= '
            <div class="card">
              <div class="card-block">
                <h3 class="card-title">'.$v['title'].'</h3>
                <p class="card-text"><a href="'.url('essay/read','item='.$v['listid']).'" title="阅读" class="text-success">'.substr($v['content'],0,200).'...</a></p>
                <p class="card-text">
                    <span class="text-info"><i class="fa fa-eye"></i> '.$v['read_count'].'</span>
                    日期:'.$v['date'].'
                    作者:<a href="'.urlBuild('!.essay','?filter=sign&value='.$v['sign']).'">'.$v['sign'].'</a>
                    '.(empty($v['collected'])? '':'文集：<a href="'.urlBuild('!.essay','?filter=collected&value='.$v['collected']).'">'.$v['collected'].'</a>').'
                </p>
              </div>
            </div>
            ';
        }
        $page = [];
        $page['list'] = $list;
        $this->assign('page',$page);
        $this->assign('pageBar',$bstp->pageBar($count));
        return $this->fetch();
    }
    // 阅读文章
    public function read(){
        $item = request()->param('item');
        $data = Db::table('atc1000c')
            ->where('listid',$item)
            ->find();
        // 阅读数处理，不重复保存数据
        $scache = new SCache();
        if($scache->has('index_art1000c_read_ctt',$item) == false){
            $count = $data['read_count'] + 1;
            $data['read_count'] = $count;
            Db::table('atc1000c')->where(['listid'=>$item])->update(['read_count'=>$count]);
            $scache->set('index_art1000c_read_ctt',$item);
        }
        $data['star_mk'] = ($scache->has('index_art1000c_star_ctt',$item) == true)? 'Y':'N';

        $this->loadScript([
            'js' => ['essay/read'],
            'title' => $data['title'].(empty($data['collected'])? '':'/'.$data['collected']).'-'.$data['sign']
        ]);

        // 评论获取
        $commentsCtt = Db::table('atc1002c')
            ->where('pid',$item)
            ->count();
        if($commentsCtt){
            $data['cmmt_ctt'] = $commentsCtt;
            $comments = Db::table('atc1002c')
                ->where('pid',$item)
                ->select();
            $commentsList = '';
            foreach ($comments as $v){
                $commentsList .= '
                <div class="list-group-item list-group-item-action flex-column align-items-start">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1">'.$v['sign'].'</h5>
                        <small>'.$v['mtime'].'</small>
                    </div>
                    <div class="mb-1">'.nl2br($v['comment']).'</div>
                </div>
                ';
            }
            $data['cmmts'] = $commentsList;
        }
        $data['content'] = nl2br($data['content']);
        $this->assign('data',$data);
        return $this->fetch();
    }
}