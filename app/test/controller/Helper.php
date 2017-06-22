<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/16 0016 14:31
 * Email: brximl@163.com
 * Name:
 */

namespace app\test\controller;


use app\common\model\Token;
use app\common\SysFile;

class Helper
{
    public function getToken(){
        //return (new Token())->access_token(['type'=>20]);
        return '无执行程序';
    }
    // 文件地址处理
    public function urltest(){
        $file = new SysFile();

        //$url = 'https://nodejs.org/static/images/interactive/nodejs-interactive.png';
//        $url = 'https://ss0.bdstatic.com/70cFvHSh_Q1YnxGkpoWK1HF6hhy/it/u=1688422151,805266835&fm=23&gp=0.jpg';
//        $file->fromUrl($url);

        //$file->save(4);
        //$file->remove([1,2,3,4,5,6,7,8]);
    }
    // 文件提交
    public function upfile(){
        echo '
        <form action="'.url('helper/urltest').'" method="post" enctype="multipart/form-data">
            <input type="file" name="test" required>
            <input type="file" name="t2">
            <button>提交</button>
        </form>
        ';
    }
}