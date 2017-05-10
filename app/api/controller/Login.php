<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/9 0009 22:25
 * Email: brximl@163.com
 * Name: 用户登录
 */

namespace app\api\controller;
use think\Session;
use think\Config;
use think\Db;
use app\common\model\User;
use app\common\controller\Api;

class Login extends Api
{
    // 用户登录认证
    public function auth(){
        // 验证码自动验证
        $account = request()->param('account');
        $pswd = request()->param('pswd');
        $code = request()->param('code');
        $msg = '';
        if(!captcha_check($code)) $msg = '验证码无效';
        else{
            $hasAccount = (new User())->AccountExist($account);
            if(!$hasAccount) $msg = '账户不存在！';
            else{
                $pswdCd = (new User())->where('account',$account)->value('certificate');
                if(md5($pswd) != $pswdCd) $msg = '密码不正确！';
            }
        }
        if(empty($msg)){
            $data = (new User())->field('uid,name,gender,account as user')->where('account',$account)->find()->toArray();
            Session::set(Config::get('setting.session_user_key'),$data);
            // 写入登记表
            $count = Db::table('sys_login')->where('uid',$data['uid'])->count();
            Db::table('sys_login')->insert([
                'uid' => $data['uid'],
                'ip'  => request()->ip(),
                'count' => ($count? ($count)+1 : 1)
            ]);
        }
        return $msg? ['code'=>-1,'msg'=>$msg]:['code'=>1,'msg'=>''];
    }
    // 开发者 不再加入到访问统计
    public function developer(){
//        $data = request()->param();
        // 令牌
        $token = request()->param('token');
        println($token);
    }
}