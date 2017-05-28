/**
 * Created by Administrator on 2017/5/25 0025.
 */
$(function () {

    /**
     * 设置项布景
     */
    function settingDs(data) {
        var id = 'setting_add_modal';
        if(data && data.listid){
            data.pk = Base64.encode(data.listid);
            delete data.listid;
        }
        var content = Web.formGroup([
            {label:'配置键名',name:'setting_key',hideCol:['pk'],notnull:1},
            {label:'分组码',name:'groupid'},
            {label:'配置短文本',name:'short_text'},
            {label:'配置长文本',name:'long_text',type:'tarea'},
            {label:'json字符串',name:'json_text'},
            {label:'备注',name:'remark'}
        ],data);
        Web.modal({
            title:'新增设置项',
            content:content,
            large:true,
            id:id,
            save:function () {
                var savedata = Web.formJson('#'+id);
                savedata.pid = Web.getUrlBind('uid');
                var dom = $(this);
                if(savedata.setting_key == ''){
                    Web.ModalAlert(dom,'【配置键名】不可为空!');
                    return;
                }else if(savedata.long_text == "" && savedata.short_text == ""){
                    Web.ModalAlert(dom,'【配置项】不可为空，必须短文本/长文本必须填一项');
                    return;
                }else if(savedata.pid == ""){
                    Web.ModalAlert(dom,'请求地址无效！');
                    return;
                }
                if(savedata.pk == "") delete savedata.pk;
                descrip = savedata.long_text? savedata.long_text:(savedata.short_text? savedata.short_text:'');
                Web.ApiRequest('project/setting_save',savedata,function (record) {
                    if(record.code == -1) Web.ModalAlert(dom,record.msg);
                    else{
                        dom.parents('div.media-body').find('div.media-descrip').html(descrip);
                        $('#'+id).modal('hide');
                        setTimeout(function () {
                            Web.modal_alert(record.msg);
                        },1000);
                    }
                });

            }
        });
    }
    // 设置项新增
    $('#setting_add_lnk').click(function () {
        settingDs();
    });
    // 设置修改
    // ? 修改后数据展示
    $('.js__sett_edit').click(function () {
       var uid = $(this).attr('data-no');
        if(uid){
            Web.ApiRequest('project/get_setting',{uid:uid},function (rdata) {
                settingDs(rdata.data);
            });
        }
    });
    // 设置删除
    $('.js__sett_del').click(function () {
        var uid = $(this).attr('data-no');
        var media = $(this).parents('li.media');
        Web.confirm('您确定要删除数据吗？',function () {
            if(uid){
                Web.ApiRequest('project/setting_save',{pk:uid,mode:'D'},function (rdata) {
                    $('#btsp_modal_confirm').modal('hide');
                    if(rdata.code == 1) media.remove();
                    else Web.modal_alert(rdata.msg);
                });
            }
        });
    });
});