/**
 * 用户注册
 * 2017年5月7日 星期日
 */
$(function () {
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
                weui.topTips(text);
                return;
            }
            $.post(Wap._baseurl+'api/register/check',{value:account,type:'account'},function (data) {
                if(data.code == -1){
                    accountInvalid = true;
                    Wap.CellWarning(dom);
                    weui.topTips(data.msg);
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

    // 表单保存
    weui.form.checkIfBlur('.js__form');
    $('#submit_lnk').click(function () {
        if(accountInvalid){
            weui.topTips("请更换【"+$('#account_contrl').val()+"】账号，避免直接使用邮箱获取手机号（可在后期绑定）！");
            return false;
        }
        weui.form.validate('.js__form', function (error) {
            if (!error) {
                var savedata = Wap.formJson('.js__form');
                if(savedata.pswdck != savedata.pswd){
                    weui.topTips("密码前后不一致");
                    return false;
                }
                var loading = weui.loading('数据保存中...');                
                $.post(Wap._baseurl+'api/register/save',savedata,function (data) {
                    loading.hide();
                    if(data.code == 1){
                        alert(data.msg);
                        location.href = Wap._homeUrl;
                    }
                    else{
                        weui.topTips(data.msg);
                        reflushCode();
                    }
                });
            }
        });
    });
});