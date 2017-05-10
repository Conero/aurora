<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/9 0009 22:25
 * Email: brximl@163.com
 * Name: 用户登录
 */

namespace app\api\controller;
use app\common\model\Token;
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
}