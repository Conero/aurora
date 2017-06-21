/**
 * Created by Administrator on 2017/6/13 0013.
 */
$(function () {
    // 右滑动时返回
    Wap.SwipeRightBack();
    
    var MastOpts = null;
    /**
     * 获取master 可选参数
     */
    function getMasterPickerOption() {
        if(!MastOpts){
            Wap.ApiRequest('finance/master_get',null,function (rdata) {
               MastOpts = rdata.data;
            });
        }
    }
    // 事务设置
    $('#master_setter_btn').click(function () {
        if(!MastOpts) getMasterPickerOption();
        if(!MastOpts) return;
        weui.picker(MastOpts,{
            className: 'custom-classname',
            onConfirm: function (result) {
                $('#master_ipter').val(result[0].label);
                $('#masterid_ipter').val(result[0].value);
            },
            id: 'singleLinePicker'
        });
    });
    // 数据保存
    weui.form.checkIfBlur('.js__form');
    $('#submit_lnk').click(function () {
        weui.form.validate('.js__form', function (error) {
            if(!error){
                Wap.ApiRequest('faccount/save',Wap.formJson('.js__form'),function (data) {
                    if(data.code == -1){
                        weui.alert(data.msg);
                        return null;
                    }
                    Wap.msg_success(data.msg,'财务系统.记账');
                });
            }
        });
    });
    getMasterPickerOption();
});