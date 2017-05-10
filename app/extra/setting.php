<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/7 0007 9:05
 * Email: brximl@163.com
 * Name: 设置选项
 */
return [
    'session_visit_key' => 'aurora_vkey',           // 访问记录session 键值
    'session_user_key' => 'aurora_ukey',           // 登录用户session 键值
    'gzh_code'      => '5400',  // 公众号
    'gzh_code_debug'=> 1,       // 公众号是否开启调试

    // 用于底部版权
    'organization' => 'Coenro',    // 机构组织， 用于底部才菜单
    'start'         => '2014',    // 起始时间
    'version'       => '1.0.9',          // 版本号
    'build'         => '20170510',       // 更新时间

    // 发布般相关设置
    'p_wapurl' => 'http://www.conero.cn/aurora/wap.html',
    
    // 前端设置
    'static_pref'=>'/aurora/public/',
    'debug_dir' => '/runtime/'
];