<?php

/**
 * Auther: Joshua Conero
 * Date: 2017/5/7 0007 22:55
 * Email: brximl@163.com
 * Name: 系统用户
 */
namespace app\common\model;
use think\Model;

class User extends Model
{
    protected $table = 'sys_user';
    protected $pk = 'uid';
}