<?php
/**
 * User: Joshua Conero
 * Date: 2017/5/6 0006 23:40
 * Email: brximl@163.com
 * Name: 用户注册
 */

namespace app\wap\controller;

use app\common\controller\Wap;
use app\common\model\User;
use hyang\Util;
class Register extends Wap
{
    public function index(){
        $this->loadScript([
            'title' => '用户注册',
            'js'    => ['register/index']
        ]);
        return $this->fetch();
    }
    // 数据保存
    public function save(){
        $data = request()->post();
        if($data['pswd'] != $data['pswdck']) return ['code'=>-1,'msg'=>'密码前后不一致！'];
        else{
            $data['certificate'] = md5($data['pswd']);
            $data = Util::dataUnset($data,['pswd','pswdck','code']);
            if(!(new User($data))->save()) return ['code'=>-1,'msg'=>'数据失败！'];
        }
        return ['code'=>1,'msg'=>'数据保存成功!'];
    }
}