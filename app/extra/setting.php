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
    'session_scounter_key' => 'aurora_sct_key',    // 系统计数器session值
    'session_cache'     => 'aurora_sckey',          // sesion 缓存键值
    'session_cache_dir'  => 'runtime/_scache/',         // session_cache 地址
    'sckey_name'        => 'api_auth',
    'session_api_sfkey'  => 'ara_sfkey',                // session API 安全机制机制处理

    'gzh_code'      => '5400',  // 公众号
    'gzh_code_debug'=> 1,       // 公众号是否开启调试
    'wap_prj_code' => 'Jessica',// 移动端项目代码
    'prj_code' => 'aurora',     // 项目代码

    // 用于底部版权
    'organization' => 'Conero',    // 机构组织， 用于底部才菜单
    'start'         => '2014',    // 起始时间
    'version'       => '0.2.7',          // 版本号
    'build'         => '20170621',       // 更新时间
    'online_date'   => '2017-05-05',                  // 上线时间
    'beian_no'      => '黔ICP备17005631号',            // 备案号

    // 发布版相关设置
    'p_wapurl' => 'http://www.conero.cn/aurora/wap.html',
    'p_baseurl'=> 'http://www.conero.cn/aurora/',           // 请求主地址

    // 财务系统设置
    'fnc_master_init' => 'Y',       // 财务系统是否自动生成事务甲方： 现金

    // 插件
    // 手机端文章分享
    'wap_thisjia'=>'<div class="jiathis_style_m"></div><script type="text/javascript" src="http://v3.jiathis.com/code/jiathis_m.js" charset="utf-8"></script>',  // 手机端文章分享
    'web_thisjia' => '
            <div class="jiathis_style">
                    <span class="jiathis_txt">分享到：</span>
                    <a class="jiathis_button_tools_1"></a>
                    <a class="jiathis_button_tools_2"></a>
                    <a class="jiathis_button_tools_3"></a>
                    <a class="jiathis_button_tools_4"></a>
                    <a href="http://www.jiathis.com/share?uid=2135664" class="jiathis jiathis_txt jiathis_separator jtico jtico_jiathis" target="_blank">更多</a>
                    <a class="jiathis_counter_style"></a>
                </div>
                <script type="text/javascript">
                    var jiathis_config = {data_track_clickback:\'true\'};
                </script>
                <script type="text/javascript" src="http://v3.jiathis.com/code/jia.js?uid=2135664" charset="utf-8"></script>
        ',
    
    // 前端设置
    'static_pref'=>'/aurora/public/',
    'url_pref'  => '/aurora/',      // 地址前缀
    'debug_dir' => '/runtime/'
];