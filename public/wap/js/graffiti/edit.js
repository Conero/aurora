/**
 * Created by Administrator on 2017/6/6 0006.
 */
$(function () {
    // 登录数据提交
    weui.form.checkIfBlur('.js__form');
    $('#save_lnk').click(function () {
        var formSel = '.js__form';
        weui.form.validate(formSel, function (error) {
            if (!error) {
                var loading = weui.loading('提交中...');
                //loading.hide();return;
                $.post(Wap._baseurl+'api/graffiti/save',Wap.formJson(formSel),function (data) {
                    loading.hide();
                    if(data.code == -1){
                        weui.alert(data.msg);
                        return;
                    }
                    Wap.ResetForm(formSel);
                    setTimeout(function () {
                        location.href = Wap._homeUrl+'#graffiti';
                    },2000);
                    weui.toast(data.msg,2000);
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