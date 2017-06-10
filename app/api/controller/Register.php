<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/9 0009 22:23
 * Email: brximl@163.com
 * Name: 用户注册接口
 */

namespace app\api\controller;
use app\common\controller\Api;
use app\common\model\User;
use hyang\Net;
use hyang\Util;
class Register extends Api
{
    // 数据保存
    public function save(){
        $data = request()->post();
        if($data['pswd'] != $data['pswdck']) return ['code'=>-1,'msg'=>'密码前后不一致！'];
        elseif (!captcha_check($data['code'])){
            return ['code'=>-1,'msg'=>'验证码无效！'];
        }else{
            $data['certificate'] = md5($data['pswd']);
            $data = Util::dataUnset($data,['pswd','pswdck','code']);
            $data['register_ip'] = Net::getNetIp();
            if(!(new User($data))->save()) return ['code'=>-1,'msg'=>'数据失败！'];
        }
        return ['code'=>1,'msg'=>'数据保存成功!'];
    }
    // 数据用户检测
    public function check(){
        $type = request()->param('type');
        $value = request()->param('value');
        switch ($type){
            case 'account': // 账号检测
                if((new User())->AccountExist($value)) return ['code'=>-1,'msg'=>'【'.$value.'】已经存在！'];
                break;
        }
    }
}