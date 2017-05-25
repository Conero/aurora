/**
 * Created by Administrator on 2017/5/25 0025.
 */
$(function () {
    // $('.nav-link').click(function (e) {
    //     e.preventDefault();
    //     $(this).tab('show');
    //     console.log(Math.random());
    // })

    // 设置项新增
    $('#setting_add_lnk').click(function () {
        var id = 'setting_add_modal';
        var content = Web.formGroup([
            {label:'配置键名',name:'setting_key',notnull:1},
            {label:'分组码',name:'groupid'},
            {label:'配置短文本',name:'short_text'},
            {label:'配置长文本',name:'long_text'},
            {label:'备注',name:'remark'}
        ]);
       Web.modal({
           title:'新增设置项',
           content:content,
           large:true,
           id:id,
           save:function () {
               var savedata = Web.formJson('#'+id);
               if(savedata.setting_key == ''){
                   Web.ModalAlert($(this),'【配置键名】不可为空!');
                   return;
               }else if(savedata.long_text == "" && savedata.short_text == ""){
                   Web.ModalAlert($(this),'【配置项】不可为空，必须短文本/长文本必须填一项');
                   return;
               }
               console.log(savedata);
               $('#'+id).modal('hide');
           }
       });
    });
});