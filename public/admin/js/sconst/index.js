/**
 * Created by Administrator on 2017/5/20 0020.
 */
$(function () {
   // 删除确认
    $('.js__del_lnk').click(function () {
        var scope = $(this).attr('data-id');
        Web.confirm('您确定要删除数据吗？',function () {
            Web.post(Web._baseurl+'admin/sconst/del',{scope:scope});
        });
    });
});
