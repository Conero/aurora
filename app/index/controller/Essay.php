<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/29 0029 22:41
 * Email: brximl@163.com
 * Name: web版本文章广场
 */

namespace app\index\controller;


use app\common\controller\Web;
use think\Db;
use app\common\SCache;

class Essay extends Web
{
    // 首页
    public function index(){
        $data = Db::table('atc1000c')
            ->where('is_private','N')
            ->order('date desc')
            ->select();
        $list = '';
        foreach ($data as $v){
            $list .= '
            <div class="card">
              <div class="card-block">
                <h3 class="card-title">'.$v['title'].'</h3>
                <p class="card-text">'.substr($v['content'],0,200).'...</p>
                <p class="card-text">
                    <a href="'.url('essay/read','item='.$v['listid']).'" title="阅读"><i class="fa fa-eye"></i> '.$v['read_count'].'</a>
                    日期:'.$v['date'].'
                    作者:'.$v['sign'].'
                </p>
              </div>
            </div>
            ';
        }
        $page = [];
        $page['list'] = $list;
        $this->assign('page',$page);
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
        $count = $data['read_count'];
        if($scache->has('index_art1000c_read_ctt',$item) == false){
            $count = $data['read_count'] + 1;
            $data['read_count'] = $count;
            Db::table('atc1000c')->where(['listid'=>$item])->update(['read_count'=>$count]);
            $scache->set('index_art1000c_read_ctt',$item);
        }
        //println($param);
        $data['content'] = nl2br($data['content']);
        $this->assign('data',$data);
        return $this->fetch();
    }
}