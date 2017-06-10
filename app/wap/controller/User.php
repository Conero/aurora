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
}