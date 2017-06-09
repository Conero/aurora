/**
 * Created by Administrator on 2017/5/8 0008.
 * 用户注册
 */
$(function () {
    // 验证码刷新
    function reflushCode() {
        var img = $('#recode_lnk').find('img');
        var src = img.attr("src");
        img.attr('src',src);
    }
    // 验证码更换
    $('#recode_lnk').click(function () {
        reflushCode();
    });
   // 登录数据提交
    weui.form.checkIfBlur('.js__form');
    $('#save_lnk').click(function () {
        weui.form.validate('.js__form', function (error) {
            if (!error) {
                var loading = weui.loading('登录中...');
                $.post(Wap._baseurl+'api/login/auth',Wap.formJson('.js__form'),function (data) {
                    loading.hide();
                    if(data.code == -1){
                        weui.alert(data.msg);
                        reflushCode();
                        return;
                    }
                    setTimeout(function () {
                        location.href = Wap._homeUrl;
                    },3000);
                    weui.toast('登录成功',2000);

                });
                /*
                setTimeout(function () {
                    loading.hide();
                    weui.toast('提交成功', 3000);
                }, 1500);
                */
            }
            // return true; // 当return true时，不会显示错误
        });
    });
});
