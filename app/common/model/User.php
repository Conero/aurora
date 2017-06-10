<?php

/**
 * Auther: Joshua Conero
 * Date: 2017/5/7 0007 22:55
 * Email: brximl@163.com
 * Name: 系统用户
 */
namespace app\common\model;
use think\Db;
use think\Model;

class User extends Model
{
    protected $table = 'sys_user';
    protected $pk = 'uid';

    /**
     * @param $account
     * @return bool
     */
    public function AccountExist($account){
        $count = $this->db()->where('account',$account)->count();
        if($count > 0) return true;
        return false;
    }

    /**
     * 获取当前登录的用户信息
     * @param null $uid
     * @return array|false|\PDOStatement|string|Model
     */
    public function getUserInfo($uid=null){
        $uid = $uid? $uid: getUserInfo('uid');
        if(empty($uid)) return [];
        $data = $this->db()
            ->where('uid',$uid)
            ->find()
            ->toArray()
        ;
        $data = array_merge($data,Db::table('sys_login')
            ->field('mtime as last_time,ip as last_ip')
            ->where('uid',$uid)
            ->order('mtime desc')
            ->find()
        );
        // 登录统计数
        $data['login_count'] = Db::table('sys_login')
            ->where('uid',$uid)
            ->count();
        return $data;
    }
}