/**
 * 用户注册
 * 2017年5月7日 星期日
 */
$(function () {
   //alert(55);
    //weui.alert(85);
    // 验证码刷新
    function reflushCode() {
        var img = $('#recode_lnk').find('img');
        var src = img.attr("src");
        img.attr('src',src);
    }
    // 账号无效标识
    var accountInvalid = false;
    // 账号检测
    $('#account_contrl').blur(function () {
        var dom = $(this);
        var account = dom.val();
        if(account != ""){
            // 合法性检测
            var reg = /^[a-z0-9_-]{3,30}$/i;
            if(!account.match(reg)){
                var text = "【"+account+"】无效，账号不符合要求！";
                accountInvalid = true;
                Wap.CellWarning(dom);
                weui.alert(text);
                return;
            }
            $.post(Wap._baseurl+'api/register/check',{value:account,type:'account'},function (data) {
                if(data.code == -1){
                    accountInvalid = true;
                    Wap.CellWarning(dom);
                    weui.alert(data.msg);
                }
            });
        }
        Wap.CellWarning(dom,true);
        accountInvalid = false;
    });
    // 验证码更换
    $('#recode_lnk').click(function () {
        reflushCode();
    });
    // 数据保存
    $('#submit_lnk').click(function () {
        var sel = 'form';
        var savedate = Wap.formJson(sel);
        if(!Wap.IsRequired(sel)){
            $.post(Wap._baseurl+'api/register/save',savedate,function (data) {
                if(data.code == 1){
                    alert(data.msg);
                    location.href = Wap._homeUrl;
                }
                else weui.alert(data.msg);
            });
            //weui.alert("数据保存失败！");
            reflushCode();
            return false;
        }
    });
});