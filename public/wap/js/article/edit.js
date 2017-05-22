/**
 * Created by Administrator on 2017/5/22 0022.
 */
$(function () {
    // 日期设置
    $('#date_setting').click(function () {
        var dt = new Date();
        weui.datePicker({
            start: 1990,
            end: 2040,
            defaultValue: [dt.getFullYear(),dt.getMonth() + 1,dt.getDate()],
            onConfirm: function(result){
                console.log(result);
                var date = result[0].value
                        + '-'
                        + result[1].value
                        + '-'
                        + result[2].value
                    ;
                $('#date_ipt').val(date);
            },
            id: 'datePicker'
        });
    });
    // 数据保存
    weui.form.checkIfBlur('.weui-cells_form');
    $('#save_data_lnk').click(function () {
        weui.form.validate('.weui-cells_form', function (error) {
            if(!error){
                Wap.ApiRequest('article/save',Wap.formJson('.weui-cells_form'),function (data) {
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
