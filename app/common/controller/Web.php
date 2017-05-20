<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/7 0007 10:35
 * Email: brximl@163.com
 * Name:
 */

namespace app\common\controller;

use think\Controller;
class Web extends Controller
{
    use \app\common\traits\Controller; // 控制器
    use \app\common\traits\DbUtil; // 数据助手
    public function _initialize(){
        $this->autoRecordVisitRecord();     // 自动统计访问量，web版
        $this->apiCheckKeys();
        $this->init();
    }
    protected function init(){}    // 应用初始化接口
}