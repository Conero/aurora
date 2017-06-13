/**
 * Created by Administrator on 2017/6/13 0013.
 */
$(function () {
    // 数据保存
    weui.form.checkIfBlur('.js__form');
    $('#submit_lnk').click(function () {
        weui.form.validate('.js__form', function (error) {
            if(!error){
                Wap.ApiRequest('faffair/save',Wap.formJson('.js__form'),function (data) {
                    if(data.code == -1){
                        weui.alert(data.msg);
                        return null;
                    }
                    Wap.msg_success(data.msg,'文章编辑');
                });
            }
        });
    });
});