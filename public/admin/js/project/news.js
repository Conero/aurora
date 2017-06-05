/**
 * Created by Administrator on 2017/6/3 0003.
 */
$(function () {
    window.page = {};
    // 删除类型设置项
    page.DelSetTypeLnk = function (dom) {
        dom = $(dom);
        dom.parents('div.form-inline').remove();
    };
    var news_type = Web.getJsVar('news_type');
    Web.tinymce('#ipt_content');
    // 更新选择器
    function UpdateTypeSel(data) {
        news_type = data;
        var option = '';
        for(var k in data){
            option += '<option value="'+k+'">'+data[k]+'</option>';
        }
        if(option != '') $('#ipt_type').html(option);
    }
    // 类型设置窗口
    $('#set_type_lnk').click(function () {
        var setId = Web.getJsVar('setId');
        if(setId == ''){
            Web.modal('类型数据加载失败，请反馈给网站！');
            return;
        }
        var content = '<p><a href="javascript:void(0);" dataid="add_btn">新增</a></p>';
        for(var k in news_type){
            content += '<div class="form-inline list-group-item"> <input class="form-control" name="key" value="'+k+'"> <input class="form-control" name="desc"  value="'+news_type[k]+'"><button type="button" onclick="page.DelSetTypeLnk(this)" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button></div>';
        }
        var id = 'set_type_ds';
        Web.modal({
            title:'设置更多类型',
            id:id,
            large:true,
            content:content,
            save:function () {
                //Web.ModalAlert($(this),'555555');
                var dom = $(this);
                var formInline = $('#'+id).find('div.modal-body div.form-inline');
                var json = {};
                for(var i=0; i<formInline.length; i++){
                    var tmp = Web.formJson($(formInline[i]));
                    var key = tmp.key;
                    var desc = tmp.desc;
                    if(key == '' || desc == ''){
                        Web.ModalAlert(dom,'数据不完整，提交已被阻止！');
                        return;
                    }
                    json[key] = desc;
                }
                var saveData = {
                    json_text: JSON.stringify(json),
                    listid:Base64.encode(setId)
                };
                Web.ApiRequest('project/setting_save',saveData,function (record) {
                    if(record.code == 1){
                        UpdateTypeSel(json);
                        $('#'+id).modal('hide');
                        return;
                    }
                    Web.ModalAlert(dom,record.msg);
                });
            }
        },null,{
            bindEvent:'add_btn',
            add_btn:function () {
            var mbody = $(this).parents('div.modal-body');
            var tmpXhtml = '<div class="form-inline list-group-item"> <input class="form-control" name="key"> <input class="form-control" name="desc"><button type="button" onclick="page.DelSetTypeLnk(this)" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button></div>';
            mbody.append(tmpXhtml);
        }
        });
    });
});