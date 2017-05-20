/**
 * Created by Administrator on 2017/5/20 0020.
 */
$(function () {
    // 删除确认
    $('.js__trash_msg').click(function () {
        var dom = $(this);
        var uid = dom.attr('data-uid');
        Web.confirm('您确定要删除数据吗?',function () {
            $('#btsp_modal_confirm').modal('hide');
            $.post(Web._baseurl + 'admin/logger/del',{'__:':Web.bsjson({uid:uid,item:'r_l_m'})},function (data) {
                if(data.code == 1) dom.parents('div.card-block').remove();
                else Web.alert(data.msg);
            });
        });
    });
});