/**
 * Created by Administrator on 2017/6/17 0017.
 */
$(function () {
    // 滑动特效
    function panelSwipe(dom) {
        var id = dom.attr('id');
        dom.toggleClass('aurora-hidden');
        var targetId = '#' + (id == 'home'? 'edit':'home');
        $(targetId).toggleClass('aurora-hidden');
    }
    // 右滑动时返回
    Wap.SwipeRightBack();
    
    var $panel = $('.weui-tab__panel');
    // tag 滑动切换 -> 查看与新增
    $panel.swipeLeft(function () {
        panelSwipe($(this));
    });



    /**
     * 自动适应描点
     * @param hash
     */
    function AdjustRequest(hash) {
        hash = hash? hash : location.hash;
        if(hash){
            var curTab = $(hash);
            if(curTab.length > 0 && curTab.attr('class').indexOf('aurora-hidden') > -1){
                $('.weui-tab__panel').addClass('aurora-hidden');
                curTab.removeClass('aurora-hidden');
            }
            location.hash = hash;
        }
    }
    AdjustRequest();
    // tab 页面跳转
    $('.js__goto').click(function () {
       var id = $(this).attr('href');
        if(id) AdjustRequest(id);
        //console.log(id);
    });
    // 编辑页面保存
    $('#edit_submit_lnk').click(function () {
        var tag = $('#tag_ipter').val();
        if(tag == ""){
            weui.topTips('请设置标签!',2000);
            return;
        }
        var loading = weui.loading('数据提交中...');
        var privateMk = $('#prvtmk_ipter').is(':checked')? 'N':'Y';
        var saveData = {tag:tag,private_mk:privateMk};
        Wap.ApiRequest('finance/tag_save',saveData,function (rdata) {
            loading.hide();
            if(rdata.code == -1){
                weui.alert(rdata.msg);
                return;
            }
            weui.toast(rdata.msg);
            // 表单重置
            $('#tag_ipter').val('');
            $('#prvtmk_ipter').attr('checked',true);
            AdjustRequest('#home');
        });
    });
});