<?php
/**
 * User: Joshua Conero
 * Date: 2017/5/6 0006 23:28
 * Email: brximl@163.com
 * Name: 用户登录
 */

namespace app\wap\controller;
use app\common\controller\Wap;
use app\common\model\User;
use think\Config;
use think\Db;
use think\Session;

class Login extends Wap
{
    public function index(){
        $this->loadScript([
            'title' => '用户登录',
            'js'    => ['login/index']
        ]);
        return $this->fetch();
    }
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
}