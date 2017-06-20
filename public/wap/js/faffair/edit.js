/**
 * Created by Administrator on 2017/6/13 0013.
 */
$(function () {
    // 右滑动时返回
    Wap.SwipeRightBack('faffair.html');
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
    // 分组可选
    $('#groupMk_sel').click(function () {
        var group = Wap.getJsVar('groups');
        console.log(group);
        var pickerOption = [];
        for(var i=0; i<group.length; i++){
            if(group[i]){
                pickerOption.push({
                    label:group[i]
                });
            }
        }
        weui.picker(pickerOption, {
            onConfirm: function (result) {
                $('#groupmk_ipter').val(result[0].label);
            }
        });
    });
});