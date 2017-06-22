<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/11 0011 16:04
 * Email: brximl@163.com
 * Name:
 */

namespace app\test\controller;
// 数据库操作

use app\common\model\Loger;
use app\common\model\Prj1001c;
use app\common\model\Token;
use app\common\SCache;
use think\Db;

class Dbq
{
    public function save(){
        //$data =
        /*
         // 系统统计
        $baseDt = ['belong_mk'=>'conero','group_mk'=>'survey','uid'=>1];
        Db::table('sys_counter')->insertAll([
            array_merge($baseDt,['listid'=>getPkValue('pk_sys_counter__listid'),'counter'=>'support','descrip'=>'支持系统统计数']),
            array_merge($baseDt,['listid'=>getPkValue('pk_sys_counter__listid'),'counter'=>'opposed','descrip'=>'不支持系统统计数']),
        ]);
        */

        
        //  // 系统日志： 2017年5月13日 星期六
        // Db::table('sys_loger')->insert([
        //     'listid'=>getPkValue('pk_sys_loger__listid'),
        //     'loger'=>'API请求之访问记录ip分析日志',
        //     'belong_mk'=>'sys_api',
        //     'code'=>'ip_ans_area',
        //     'type'=>'S',
        //     'uid'=>1
        // ]);


        //  // 系统日志： 2017年5月20日 星期六
        // Db::table('sys_loger')->insert([
        //     'listid'=>getPkValue('pk_sys_loger__listid'),
        //     'loger'=>'系统文件日志删除记录',
        //     'belong_mk'=>'sys_admin',
        //     'code'=>'log_sfile',
        //     'type'=>'S',
        //     'uid'=>1
        // ]);

//        // 系统令牌 2017年5月31日 星期三
//        (new Token())->save([
//            'listid' => getPkValue('pk_sys_token__listid'),
//            'type' => '20',
//            'token' => 'wx170505'
//        ]);
    }
    public function test(){
        // 1*
//        (new Loger())->write('ip_ans_area','数据日志测试');

        // 2*
//       $sc = new SCache();
//       $sc->set('yang',7);
//       $sc->set('yang','yh');
//       $sc->set('hua',[1,5,8,6]);
//       $sc->set('hua',[47]);
//       println(
//           $sc->has('yang',7),
//           $sc->has('yang',10),
//           $sc->has('hua',47),
//           $sc->has('hua',5),
//           $sc->has('hua','yjjj'),
//           $sc->has('yang','yh')
//           );


        // 2*
        //echo (new Prj1001c())->getSetVal('weixin_api.cmd_list_help','Jessica',true);
        // ***           
        println("无执行代码");
    }
}