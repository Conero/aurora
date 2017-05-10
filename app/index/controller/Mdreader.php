<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/10 0010 23:07
 * Email: brximl@163.com
 * Name: 桌面版 markdown 文件解析
 */

namespace app\index\controller;


use app\common\controller\Web;
import('Parsedown',EXTEND_PATH);

class Mdreader extends Web
{
    /**
     * request_data:  fmd 文件名称， title 一级菜单; subtitle 二级菜单
     * @return mixed
     */
    public function index(){
        // flie.md
        $file = request()->param('fmd');
        $content = '';
        if($file){
            $sfile = ROOT_PATH.'/'.$file.'.md';
            if(!is_file($sfile)) $sfile = ROOT_PATH.'/'.$file;
            if(!is_file($sfile)) $sfile = $file;
            if(is_file($sfile))
                $content = (new \Parsedown())
                    ->text(file_get_contents($sfile));
            else
                $content = '文件读取失败';
        }
        $page = ['content'=>$content];
        // 主菜单
        $title = request()->param('title');
        if($title) $page['title'] = $title;
        // 子菜单
        $title = request()->param('subtitle');
        if($title) $page['subtitle'] = $title;

        $this->assign('page',$page);
        return $this->fetch();
    }
}