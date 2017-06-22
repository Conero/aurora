/**
 * Created by Administrator on 2017/6/22 0022.
 */
$(function () {
    // 右滑动时返回
    Wap.SwipeRightBack('fplan.html');

    // 表单提交
    weui.form.checkIfBlur('.js__form');
    $('#submit_lnk').click(function () {
        weui.form.validate('.js__form', function (error) {
            if(!error){
                Wap.ApiRequest('fplan/save',Wap.formJson('.js__form'),function (data) {
                    if(data.code == -1){
                        weui.alert(data.msg);
                        return null;
                    }
                    Wap.msg_success(data.msg,'财务计划');
                });
            }
        });
    });
});