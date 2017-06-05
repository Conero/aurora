<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/6/5 0005 22:53
 * Email: brximl@163.com
 * Name: 信息公告
 */

namespace app\wap\controller;


use app\common\controller\Wap;
use app\common\model\Prj1002c;
use app\common\SCache;
use think\Config;

class Inform extends Wap
{
    public function index(){
        $inform_list = '';
        $prj2 = new Prj1002c();
        $data = $prj2->getInfos(Config::get('setting.prj_code'),false,function ($query){
            return $query->limit(30)->select();
        });
        foreach ($data as $v){
            $inform_list .= '
                <div class="weui-media-box weui-media-box_text">
                    <h4 class="weui-media-box__title">'.$v['title'].'</h4>
                    <p class="weui-media-box__desc">
                        <a href="'.url('inform/read','item='.$v['listid']).'">'.$v['descrip'].'</a>
                        <i class="fa fa-eye"></i> '.$v['read_count'].'
                    </p>
                    <ul class="weui-media-box__info">
                        <li class="weui-media-box__info__meta">时间</li>
                        <li class="weui-media-box__info__meta">'.$v['mtime'].'</li>
                    </ul>
                </div>
               ';
        }
        $this->assign('inform_list',$inform_list);
        return $this->fetch();
    }
    public function read(){
        $item = request()->param('item');
        $prj2 = new Prj1002c();
        $page = $prj2->get($item)->toArray();

        $scache = new SCache();
        $count = $page['read_count'];
        if($scache->has('wap_inform_read_ctt',$item) == false){
            $count = $count + 1;
            $page['read_count'] = $count;
            $prj2->save(['read_count'=>$count],['listid'=>$item]);
            $scache->set('wap_inform_read_ctt',$item);
        }

        $this->assign('page',$page);
        return $this->fetch();
    }
}