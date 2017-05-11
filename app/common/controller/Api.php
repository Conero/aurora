<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/9 0009 21:29
 * Email: brximl@163.com
 * Name: 公共接口公共函数
 */

namespace app\common\controller;
use app\common\traits\DbUtil;
use app\common\traits\Util;
use think\Controller;

class Api extends Controller
{
    use DbUtil; // 数据库助手
    use Util;   // 公共方法， Web, Api, Wap

    /**
     * 信息反馈
     * @param $msg
     * @param null $code 默认为 失败
     * @return \think\response\Json
     */
    protected function FeekMsg($msg,$code=null){
        $data = [
            'code'=>($code? $code: -1),
            'msg' => $msg
        ];
        return json($data);
    }
}