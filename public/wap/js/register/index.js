/**
 * 用户注册
 * 2017年5月7日 星期日
 */
$(function () {
   //alert(55);
    //weui.alert(85);
    // 验证码更换
    $('#recode_lnk').click(function () {
        var img = $(this).find('img');
        var src = img.attr("src");
        img.attr('src',src);
    });
    // 数据保存
    $('#submit_lnk').click(function () {
        var sel = 'form';
        var savedate = Wap.formJson(sel);
        if(!Wap.IsRequired(sel)){
            $.post(Wap._baseurl+'wap/register/save',savedate,function (data) {
                if(data.code == 1){
                    alert(data.msg);
                    location.href = Wap._homeUrl;
                }
                else weui.alert(data.msg);
            });
            weui.alert("数据保存失败！");
            return false;
        }
    });
});