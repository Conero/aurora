<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/13 0013 22:23
 * Email: brximl@163.com
 * Name: 系统管理后台
 */

namespace app\admin\controller;


use app\common\controller\Web;

class Index extends Web
{
    public function index()
    {
        return $this->fetch();
    }
}