/**
 * Created by Administrator on 2017/5/11 0011.
 */
$(function () {
    function reflushCode() {
        var img = $('#recode_lnk').find('img');
        var src = img.attr("src");
        img.attr('src',src);
    }
    var feekTypePicker = [];
    var feektype = Wap.getJsVar('feek_type');
    for(var k in feektype){
        feekTypePicker.push({label:feektype[k],value:k});
    }
    // 类型选择器
    $('#type_picker').click(function () {
        // 单列picker
        weui.picker(feekTypePicker, {
            className: 'custom-classname',
            defaultValue: [3],
            // onChange: function (result) {
            //     $(result[]
            // },
            onConfirm: function (result) {
                $('#type_picker_desc').val(result[0].label);
                $('#type_picker_ipter').val(result[0].value);
            },
            id: 'type_picker'
        });
    });
    // 验证码替换
    $('#recode_lnk').click(function () {
        reflushCode();
    });
    // 数据保存
    weui.form.checkIfBlur('.weui-cells_form');
    $('#save_data_lnk').click(function () {
        weui.form.validate('.weui-cells_form', function (error) {
            if(!error){
                Wap.ApiRequest('feek/report',Wap.formJson('.weui-cells_form'),function (data) {
                    if(data.code == -1){
                        reflushCode();
                        weui.alert(data.msg);
                        return null;
                    }
                    Wap.msg_success(data.msg,'系统反馈','wap:feekback/edit');
                    /*
                   weui.toast(data.msg);
                    reflushCode();
                    // 刷新页面
                    window.setTimeout(function () {
                        window.location.reload();
                    },3000);
                    */
                });
            }
        });
    });
});