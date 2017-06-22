<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/10 0010 21:37
 * Email: brximl@163.com
 * Name:
 */

namespace app\test\controller;
// 导入微信开发包
import('phpqrcode.phpqrcode',EXTEND_PATH);

class Code2d
{
    public function index(){
        //header('content-type:"application/image";');
        \QRcode::png('lieke');
        exit;
    }
    public function me2(){
        $url = '77744';
        $level=3;
        $size=4;
        $errorCorrectionLevel =intval($level) ;//容错级别
        $matrixPointSize = intval($size);//生成图片大小
//生成二维码图片
        //echo $_SERVER['REQUEST_URI'];
        $object = new \QRcode();
        $object->png($url, false, $errorCorrectionLevel, $matrixPointSize, 2);
    }
}