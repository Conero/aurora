/**
 * Created by Administrator on 2017/5/28 0028.
 */
$(function () {
   // popup 窗
    $('#pid_sel_btn').click(function () {
        var pupopId = 'prj_select_pid';
       Web.pupop({
           field:{code:'代码',listid:'hidden',name:'名称'},
           post:{table:'prj1000c',order:'code'},
           pupopId:pupopId,
           single: true
       },{
           selected:function(){
               var dom = $(this);
               var tr = dom.parents('tr.datarow');
               var code = tr.find('[col-name="code"]').text();
               var lid = tr.find('[col-name="listid"]').text();
               $('#ipt_pid_name').val(code);
               $('#ipt_pid').val(lid);
               $('#prj_select_pid').modal('hide');
           }
       });
    });
});