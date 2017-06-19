<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/9 0009 22:23
 * Email: brximl@163.com
 * Name: 用户注册接口
 */

namespace app\api\controller;
use app\common\Aurora;
use app\common\controller\Api;
use app\common\model\User;
use hyang\Net;
use hyang\Util;
class Register extends Api
{
    // 数据保存
    public function save(){
        $data = request()->post();
        if($data['pswd'] != $data['pswdck']) return $this->FeekMsg('密码前后不一致');
        elseif (!captcha_check($data['code'])){
            return $this->FeekMsg('验证码无效');
        }else{
            $userModel = new User();
            // 账号检测
            if(empty($data['account']) || $userModel->AccountExist($data['account']))
                return $this->FeekMsg('账号【'.$data['account'].'】无效，请重新设置');
            $data['salt'] = Util::randStr(10);
            $data['certificate'] = Aurora::checkUserPassw($data['pswd'],null,$data['salt']);
            $data = Util::dataUnset($data,['pswd','pswdck','code']);
            $data['register_ip'] = Net::getNetIp();
            if($userModel->save($data)) return $this->FeekMsg('数据保存成功',1);
            return $this->FeekMsg('账号【'.$data['account'].'】注册失败了，十分遗憾');
        }
    }
    // 数据用户检测
    public function check(){
        $type = request()->param('type');
        $value = request()->param('value');
        switch ($type){
            case 'account': // 账号检测
                if((new User())->AccountExist($value)) return $this->FeekMsg('【'.$value.'】已经存在');
                break;
        }
    }
}