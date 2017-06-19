/**
 * Created by Administrator on 2017/6/19 0019.
 */
$(function () {
    // 头像上传监听
    $('#user_portrait').change(function () {
       var file = $(this).val();
        if(file){
            var src = this.files[0];
            var pref = $('#user_img_pref')[0];
            var reader  = new FileReader();
            reader.addEventListener("load", function () {
                pref.src = reader.result;
                var cell = $('#img_select_state');
                cell.find('.weui-media-box__desc').html('已经选择一张图片'+file+' ');
                cell.find('.weui-media-box__info').html('');
            }, false);
            reader.readAsDataURL(src);
        }
    });
    // 上传按钮
    $('#portrait_sbmt').click(function () {
        var file = $('#user_portrait').val();
        if(file) {
            var load = weui.loading('文件上传中……');
            $.ajax({
                async:true,
                type:'post',
                url:Wap._baseurl+'api/user/portrait',
                data: new FormData($('form')[0]),
                processData: false,
                contentType: false,
                success: function(data){
                    load.hide();
                    if(data.code == 1) $('#user_portrait').val('');
                    weui.toast(data.msg);
                },
                error: function(xhr, type){
                    load.hide();
                    weui.topTips('文件上传错处了，可能网站出现问题！');
                }
            });
        }else weui.topTips('请选择一张图片！');
    });
});