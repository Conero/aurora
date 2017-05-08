/**
 * Created by Administrator on 2017/5/8 0008.
 * 首页-js
 */
$(function () {
    // 用户界面
    function UserPageFn($body) {
        var xhtml = '<div class="weui-grids">'
            + '<a href="javascript:;" class="weui-grid">个人中心</a>'
            + '<a href="javascript:;" class="weui-grid">财务管理</a>'
            + '<a href="javascript:;" class="weui-grid">家谱应用</a>'
            + '<a href="javascript:;" class="weui-grid">账户管理</a>'
            + '<a href="javascript:;" class="weui-grid">日志系统</a>'
            + '<a href="javascript:;" class="weui-grid">个人计划</a>'
            +'</div>';
        $body.append(xhtml);
    }
    /**
     * tab 页面自动恢复
     */
    function tabPageCreate(url,dom) {
        if(url){
            dom = dom? dom: $('div.weui-tab').find('div.weui-tabbar').find('a[data-url="'+url+'"]');
            var activeBar = dom.parent('div.weui-tabbar').find('a.weui-bar__item_on');
            activeBar.removeClass('weui-bar__item_on');

            var curPanel = $('#'+url);
            // 是当前的tab 不重复生成
            if(curPanel.length > 0 && curPanel.attr('class').indexOf('weui-bar__item_on')>-1) return;
            var tab = $('div.weui-tab');
            tab.find('div.weui-tab__panel').hide();
            if(curPanel.length > 0) curPanel.show();
            else{
                var xhtml = '<div class="weui-tab__panel" id="'+url+'"></div>';
                tab.prepend(xhtml);
                switch(url){
                    case 'user':
                        UserPageFn($('#'+url));
                        break;
                    // case 'about':
                    //     AboutPageFn($('#'+url));
                    //     break;
                }
            }
            location.href = location.origin + location.pathname + '#' + url;
            dom.addClass('weui-bar__item_on');
        }
    }
    // 自动恢复
    if(location.hash != '')
        tabPageCreate(location.hash.substr(1));

   // 底部按钮切换
    $('.weui-tabbar__item').click(function () {
        var dom = $(this);
        var url = dom.attr('data-url');
        tabPageCreate(url,dom);
    });
});