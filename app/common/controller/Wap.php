<?php
/**
 * User: Joshua Conero
 * Date: 2017/5/6 0006 12:13
 * Email: brximl@163.com
 * Name: 移动端公共控制器
 */
namespace app\common\controller;
use Phinx\Config;
use think\Controller;
use think\Session;

class Wap extends Controller
{
    use \app\common\traits\Controller; // 控制器
    use \app\common\traits\DbUtil; // 数据助手
    public function _initialize(){
        $this->autoRecordVisitRecord();     // 自动统计访问量，手机版
        $this->apiCheckKeys();
        $this->init();
    }
    public function init(){}    // 应用初始化接口
}