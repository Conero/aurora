<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/9 0009 21:22
 * Email: brximl@163.com
 * Name: 微信服务接口
 */

namespace app\api\controller;

use app\common\controller\Api;
use think\Config;
use WechatAurora;
use app\common\model\Loger;
include __DIR__.'/WechatAurora.php';
header('content-type:text/html;charset=utf-8;');
class Weixin extends Api
{
    // 请求页面
    public function index(){
        $config = $this->getSysConst(Config::get('setting.gzh_code'));
        $config['debug'] = Config::get('setting.gzh_code_debug');
        $config['debug'] = $config['debug']? true:false;
        $wx = new WechatAurora($config);
        $log = new Loger();
        $content = request()->ip().
            ">请求时订阅号后台数据!";
        $log->write($this->LogCode,$content);
        $wx->run();
    }
}