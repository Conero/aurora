<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/6/10 0010 0:11
 * Email: brximl@163.com
 * Name: 移动个人中心
 */

namespace app\wap\controller;


use app\common\Aurora;
use app\common\controller\Wap;
use app\common\model\User as UserModel;
use think\Config;

class User extends Wap
{
    // 首页
    public function index(){
        $this->checkAuth();
        $user = new UserModel();
        $data = $user->getUserInfo();
        $this->assign('data',$data);
        return $this->fetch();
    }
    // 新增信息编辑
    public function edit(){
        $this->loadScript([
            'js' => 'user/edit'
        ]);
        $this->checkAuth();
        $user = new UserModel();
        $uid = getUserInfo('uid');
        $data = $user->get($uid)->toArray();
        $page = [];
        $city = Aurora::visitSession('city');
        if(empty($city)){
            $location = Aurora::location();
            if(empty($location['code'])){
                $city = $location['data']['city'];
                Aurora::visitSession('city',$city);
            }
        }
        if(empty($data['city'])) $data['city'] = $city;
        if($city) $page['city'] = $city;
        $this->assign('data',$data);
        $this->assign('page',$page);
        return $this->fetch();
    }
    // 密码修改
    public function password(){
        $this->loadScript([
            'title' => '修改密码 | 个人中心',
            'js'    => ['user/password']
        ]);
        return $this->fetch();
    }
    // 头像
    public function portrait(){
        $this->loadScript([
            'title' => '头像 | 个人中心',
            'js'    => ['user/portrait']
        ]);
        $page = [];
        $userMd = new UserModel();
        $page['src'] = $userMd->getPortrait();
        if(empty($page['src'])) $page['src'] = Config::get('setting.static_pref').'img/user_default.png';
        $this->assign('page',$page);
        return $this->fetch();
    }
}