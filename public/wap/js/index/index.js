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
    // 涂鸦
    var GraffitiIsLoadMK = false;
    function GraffitiPageFn() {
        if(GraffitiIsLoadMK == false){
            var loading = weui.loading('数据加载中...');
            Wap.ApiRequest('graffiti/get',null,function(rdata){
                if(rdata.code == 1){
                    GraffitiIsLoadMK = true;
                    var data = rdata.data;
                    var xhtml = '';
                    for(var i=0; i<data.length; i++){
                        var el = data[i];
                        xhtml += '<div class="weui-media-box weui-media-box_text">'
                            + '<h4 class="weui-media-box__title">'+(el.sign? el.sign:el.city) +' '+ el.mtime+'</h4>'
                            + '<p class="weui-media-box__desc">'+el.content+'</p>'
                        + '<ul class="weui-media-box__info">'
                            //+ '<li class="weui-media-box__info__meta">文字来源</li>'
                            //+ '<li class="weui-media-box__info__meta">时间</li>'
                            + '<li class="weui-media-box__info__meta weui-media-box__info__meta_extra">'+el.address+'</li>'
                            + '</ul>'
                            + '</div>';
                    }
                    $('#graffiti_panel').html(xhtml);
                    loading.hide();
                }
            });
        }
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
                    // case 'graffiti':
                    //     GraffitiPageFn();
                    //     break;
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
        if(url == 'graffiti') GraffitiPageFn();
    });
    GraffitiPageFn();
    // 涂鸦弹出菜单
    $('#gft_menu_lnk').click(function () {
        weui.actionSheet([
            {
                label: '涂鸦',
                onClick: function () {
                    location.href = Wap._baseurl+'wap/graffiti/edit.html';
                }
            }
        ], [
            {
                label: '取消',
                onClick: function () {
                    console.log('取消');
                }
            }
        ], {
            className: 'custom-classname'
        });
    });
});