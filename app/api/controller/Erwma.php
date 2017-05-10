<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/10 0010 22:02
 * Email: brximl@163.com
 * Name: 二维码生成接口
 */

namespace app\api\controller;
// php- 二维码处理库
use think\Config;

import('phpqrcode.phpqrcode',EXTEND_PATH);

class Erwma
{
    // 二维码主生成器
    public function index(){
        $text = request()->param('code');
        $type = request()->param('type');
        // 掩码方式
        if($type == 'b64' && $text) $text = base64_decode($text);
        $this->qrcode($text);
    }
    // 手机端二维码
    public function wapurl(){
        $this->qrcode(Config::get('setting.p_wapurl'));
    }
    /**
     * 二维码生成方法
     * @param $text
     */
    private function qrcode($text){
        $text = $text? $text: "It's a bad Code, Telling by Joshua Coerne!";
        \QRcode::png($text);
        exit;
    }
}