/**
 * Created by Administrator on 2017/5/11 0011.
 */
$(function () {
    $('#type_picker').click(function () {
        // 单列picker
        weui.picker([
            {
                label: '建议',
                value: 0
            },
            {
                label: '问题',
                value: 1
            },
            {
                label: '留言',
                value: 3
            },
            {
                label: 'bug',
                value: 3
            },
            {
                label: '其他',
                value: 4,
            }
        ], {
            className: 'custom-classname',
            defaultValue: [3],
            onChange: function (result) {
                console.log(result)
            },
            onConfirm: function (result) {
                console.log(result)
            },
            id: 'type_picker'
        });
    });

});