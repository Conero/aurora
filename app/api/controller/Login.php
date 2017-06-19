<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/9 0009 22:25
 * Email: brximl@163.com
 * Name: 用户登录
 */

namespace app\api\controller;
use app\common\Aurora;
use app\common\model\Token;
use hyang\Net;
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
        $account = trim(request()->param('account'));
        $pswd = trim(request()->param('pswd'));
        $code = trim(request()->param('code'));
        $msg = '';
        $userModel = new User();
        if(!captcha_check($code)) $msg = '验证码无效';
        else{
            if(!$userModel->AccountExist($account)) $msg = '账户不存在！';
            else{
                if(!Aurora::checkUserPassw(
                    $pswd,
                    $userModel->getPassword(),
                    $userModel->where('uid',$userModel->uid)->value('salt')
                )) $msg = '密码不正确！';
            }
        }
        if(empty($msg)){
            $data = $userModel->field('uid,name,gender,account as user')->where('uid',$userModel->uid)->find()->toArray();
            Session::set(Config::get('setting.session_user_key'),$data);
            // 写入登记表
            $count = Db::table('sys_login')->where('uid',$data['uid'])->count();
            $ip = Net::getNetIp();
            Db::table('sys_login')->insert([
                'uid' => $data['uid'],
                'ip'  => $ip,
                'count' => ($count? ($count)+1 : 1)
            ]);
        }
        return $msg? ['code'=>-1,'msg'=>$msg]:['code'=>1,'msg'=>''];
    }
    /**
     * 开发者首页统计过滤
     * 参数： token * , url 自动跳转
     */
    public function developer(){
        // 令牌
        $token = request()->param('token');
        $url = request()->param('url');
        $msg = '';
        $badMsg = '开发者登入网站时，令牌无效！如果无令牌请向网站申请，且该权限只向开发者开放！';
        if($token){
            $isValid = (new Token())
                ->TokenIsValid($token);
            if($isValid){
                $this->autoRecordVisitRecord(false);
                // 跳转到首页
                if($url && 'home'== $url){
                    $this->redirect($this->getHomeUrl(true));
                }
                elseif($url) $this->redirect($url);
                else $this->redirect($this->getRootUrl());
            }
            else $msg = $badMsg;
        }
        else $msg = $badMsg;
        if($url){
            $this->getErrorUrl($msg);
        }
        return json([
            'code'=>($msg? -1: 1),
            'msg' => $msg? $msg:'认证成功！'
        ]);
    }

    /**
     * 系统注销
     */
    public function quit(){
        $key = Config::get('setting.session_user_key');
        if(Session::has($key)){
            Session::delete($key);
        }
        $this->getRootUrl(false);
    }
}