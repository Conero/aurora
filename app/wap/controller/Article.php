<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/22 0022 7:26
 * Email: brximl@163.com
 * Name: 文章
 */

namespace app\wap\controller;


use app\common\controller\Wap;
use app\common\model\Atc1000c;
use app\common\SCache;

class Article extends Wap
{
    // 首页
    public function index(){
        $this->loadScript([
            'title' => '文章'
        ]);
        $uid = getUserInfo('uid');
        if($uid){
            // 用户信息
            $page = ['uid'=>$uid];
            $this->assign('page',$page);
        }
        $atc = new Atc1000c();
        // 数据提取
        $data = $atc
            ->field('left(content,50) as content,listid,title,date,collected,sign,ifnull(read_count,1) as read_count')
            ->page(1,30)
            ->where('is_private','N')
            ->order('mtime desc')
            ->select();
        if($data){
            $list = '';
            foreach ($data as $v){
                $list .= '
                    <div class="weui-media-box weui-media-box_text">
                        <h4 class="weui-media-box__title">'.$v['title'].'</h4>
                        <p class="weui-media-box__desc">
                            <a href="'.url('article/read','item='.$v['listid']).'">'.$v['content'].'...</a>
                            <i class="fa fa-eye"></i> '.$v['read_count'].'
                        </p>
                        <ul class="weui-media-box__info">
                            <li class="weui-media-box__info__meta"><i class="fa fa-user-circle"></i> '.$v['sign'].'</li>
                            <li class="weui-media-box__info__meta">'.$v['date'].'</li>
                            '.(empty($v['collected'])? '':'<li class="weui-media-box__info__meta"><i class="fa fa-book"></i> 文集：'.$v['collected'].'</li>').
                        '
                        </ul>
                    </div>
               ';
            }
            $this->assign('atclist',$list);
        }
        return $this->fetch();
    }
    // 写文章
    public function edit(){
        $this->checkAuth();
        $this->loadScript([
            'js' => 'article/edit'
        ]);
        $uid = getUserInfo('uid');
        $atc = new Atc1000c();
        // 用户信息
        $page = ['uid'=>$uid];
        // 获取可选文集以及署名
        $collected = $atc->getCollecteds($uid);
        $sign = $atc->getSigns($uid);
        if($collected){
            $this->_JsVar('collected',$collected);
            list($collected) = array_keys($collected);
        }
        if($sign){
            $this->_JsVar('sign',$sign);
            list($sign) = array_keys($sign);
        }
        // 获取可选文集
        $item = request()->param('item');
        $data = ['date'=>date('Y-m-d')];
        if($item){
            $data = $atc->where('listid',$item)->find()->toArray();
            $this->assign('pk_ipt',$this->formPkGrid($data));
        }else{
            if($collected) $data['collected'] = $collected;
            if($sign) $data['sign'] = $sign;
        }
        $this->assign('page',$page);
        $this->assign('data',$data);
        return $this->fetch();
    }
    // 阅读文章
    public function read(){
        $item = request()->param('item');
        $atc = new Atc1000c();
        $page = $atc
            ->where('listid',$item)
            ->find()
            ->toArray()
            ;
        $scache = new SCache();
        $count = $page['read_count'];
        if($scache->has('wap_art1000c_read_ctt',$item) == false){
            $count = $count + 1;
            $page['read_count'] = $count;
            $atc->save(['read_count'=>$count],['listid'=>$item]);
            $scache->set('wap_art1000c_read_ctt',$item);
        }
        $this->assign('page',$page);
        return $this->fetch();
    }
    // 我的文章
    public function mine(){
        $this->checkAuth();
        $uid = $this->getUserInfo('uid');
        $atc = new Atc1000c();
        // 文集
        $collected = $atc
            ->field('collected,count(collected) as ctt')
            ->where(['uid'=>$uid])
            ->group('collected')
            ->select();
        if($collected) $this->assign('collected',$collected);
        // 文章列表
        $data = $atc
            ->where(['uid'=>$uid])
            ->order('mtime desc')
            ->limit(20)
            ->select();
        if($data){
            $list = '';
            foreach ($data as $v){
                $list .= '
                    <div class="weui-media-box weui-media-box_text">
                        <h4 class="weui-media-box__title">'.$v['title'].'</h4>
                        <p class="weui-media-box__desc">
                            <a href="'.url('article/read','item='.$v['listid']).'"><i class="fa fa-eye"></i>阅读</a>
                            <a href="'.url('article/edit','item='.$v['listid']).'"><i class="fa fa-pencil-square"></i>编辑</a>
                        </p>
                        <ul class="weui-media-box__info">
                            <li class="weui-media-box__info__meta">'.$v['sign'].'</li>
                            <li class="weui-media-box__info__meta">'.$v['date'].'</li>
                            '.(empty($v['collected'])? '':'<li class="weui-media-box__info__meta">文集：'.$v['collected'].'</li>').
                    '
                        </ul>
                    </div>
               ';
            }
            $this->assign('atclist',$list);
        }
        return $this->fetch();
    }
}