<?php

/**
 * Auther: Joshua Conero
 * Date: 2017/5/7 0007 22:55
 * Email: brximl@163.com
 * Name: 系统用户
 */
namespace app\common\model;
use hyang\Validate;
use think\Config;
use think\Db;
use think\Model;

class User extends Model
{
    protected $table = 'sys_user';
    protected $pk = 'uid';
    public $uid;
    /**
     * @param $account
     * @return bool
     */
    public function AccountExist($account){
        // 通过账户获取用户ID
        $uid = $this->db()->where('account',$account)->value('uid');
        // 通过邮箱获取用户ID
        if(empty($uid) && Validate::isEmail($account)){
            $uid = $this->db()->where('email',$account)->value('uid');
        }
        // 通过手机获取用户ID
        if(empty($uid)){
            $uid = $this->db()->where('phone',$account)->value('uid');
        }
        if($uid) $this->uid = $uid;
        if($uid) return true;
        return false;
    }
    /**
     * 获取用户密码
     * @return mixed
     */
    public function getPassword(){
        if($this->uid) return $this->db()->where('uid',$this->uid)->value('certificate');
        return '';
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
        if($data['portrait']){
            $data['portrait'] = $this->getPortrait($data['portrait'],true);
        }
        // 登录统计数
        $data['login_count'] = Db::table('sys_login')
            ->where('uid',$uid)
            ->count();
        return $data;
    }

    /**
     * 获取头像
     * @param null $uid
     * @param bool $isPortraitId true 表示$uid 为获取到的 PortraitId 否则为 $uid
     * @return string
     */
    public function getPortrait($uid=null,$isPortraitId=false){
        $uid = $uid? $uid: getUserInfo('uid');
        if(empty($uid)) return '';
        $PortraitId = $isPortraitId? $uid: $this->db()->where('uid',$uid)->value('portrait');
        if(empty($PortraitId)) return '';
        $tmpData = Db::table('sys_file')
            ->field('path as src')
            ->where('listid',$PortraitId)
            ->find()
        ;
        return Config::get('setting.url_pref') . 'source/'. $tmpData['src'];
    }
}