<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/29 0029 21:05
 * Email: brximl@163.com
 * Name:
 */

namespace app\test\controller;


use app\common\model\Prj1001c;
use hyang\Net;
use hyang\Validate;
use hyang\Util;

class Index
{
    public function index(){
        // 项目管理测试
        $prj1c = new Prj1001c();
        println(
            $prj1c->getSetVal('index.top_descrip','Jessica',true)
            ,$prj1c->getSetVal('index.top_descrip')
        );
        println(
            $prj1c->getSetVals('index')
        );
    }
    /**
    * php 信息
    **/
    public function php(){
        phpinfo();
    }
    public function test(){
        println(
            Net::getNetIp()
        );
    }
    /**
     * hyang/Validate
     */
    public function hyang(){
        $pswd = 'hola,amo&2017';
        $salt = Util::randStr();
        $ed = crypt($pswd);
        $en = crypt($pswd,$salt);
        println(
            Validate::ipv4('127.0.0.1')
            //,Validate::ipv4('275.066.08.178')
            //,Validate::ipv4('18798011264')
            //,Validate::isEmail('brximl@163.com')
            //,Validate::isEmail('http://127.0.0.1/aurora/test/index/hyang')
            //,Validate::isEmail('test.iju')
            ,Util::randStr(10)
            ,crypt($pswd)
            ,$en
            ,   $ed == crypt($pswd,$ed)
            ,   $en == crypt($pswd,$en)
            ,   crypt($pswd,$salt) == crypt($pswd,crypt($pswd,$salt))
            ,   $en == crypt($pswd,$ed)
        );
    }
}