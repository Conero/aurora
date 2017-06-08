/**
 * Created by Administrator on 2017/6/8 0008.
 */
$(function () {
    // 当前显示
    var ListPage = 1,
        isNoMore = false    // 没有更多了
        ;
    // 加载更多
   $('#load_more_lnk').click(function () {
       /*
       // 顶部检测失败
        alert($(window).scrollTop()
            + ','
            + $(document).height()
            + ','
            + $(window).height()
        );
        */
       if(isNoMore == true){
            weui.toast('没有更多数据了，朋友!');
           return true;
       }
       ListPage = ListPage + 1;
       var dom = $(this);
       var loading = weui.loading('数据加载中...');
        $.post(Wap._baseurl+'wap/article/get_more_arts',{page:ListPage},function (rdata) {
            if(rdata.length == 0){
                isNoMore = true;
                dom.text('没有更多……');
                dom.addClass('weui-btn_plain-disabled');
                loading.hide();
                return;
            }
            var artList = '';
            var baseurl = Wap._baseurl+'wap/article/read/item/';
            for(var i=0; i<rdata.length; i++){
                var el = rdata[i];
                artList += '<div class="weui-media-box weui-media-box_text">'
                    + '<h4 class="weui-media-box__title">'+el.title+'</h4>'
                + '<p class="weui-media-box__desc">'
                    + '<a href="'+ baseurl+ el.listid+'.html">'+el.content+'...</a>'
                + '<i class="fa fa-eye"></i> '+el.read_count
                + '</p>'
                + '<ul class="weui-media-box__info">'
                    + '<li class="weui-media-box__info__meta"><i class="fa fa-user-circle"></i> '+el.sign+'</li>'
                + '<li class="weui-media-box__info__meta">'+el.date+'</li>'
                + (el.collected ? '':'<li class="weui-media-box__info__meta"><i class="fa fa-book"></i> 文集：' + el.collected+'</li>')
                + '</ul>'
                + '</div>'
                ;
            }
            $('#article_list').append(artList);
            loading.hide();
        });
   }) ;
});