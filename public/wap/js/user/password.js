/**
 * Created by Administrator on 2017/6/18 0018.
 */
$(function () {
    // 表单保存
    weui.form.checkIfBlur('.js__form');
    $('#submit_lnk').click(function () {
        weui.form.validate('.js__form', function (error) {
            if (!error) {
                var loading = weui.loading('数据保存中...');
                var savedata = Wap.formJson('.js__form');
                console.log(savedata);
                if(savedata.npassw != savedata.npassw_ck){
                    weui.topTips('新密码前后不一致，请仔细确认');
                    loading.hide();
                    return;
                }
                Wap.ApiRequest('user/passw', savedata, function (data) {
                    loading.hide();
                    if(data.code == '1'){
                        weui.toast(data.msg);
                        Wap.ResetForm('.js__form');
                    }else weui.topTips(data.msg);
                });
            }
        });
    });
});