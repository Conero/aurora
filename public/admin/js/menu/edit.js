/**
 * Created by Administrator on 2017/5/17 0017.
 */
$(function () {
    // 表单控件
    var formGrid = new FormListener('.js__form');
    var fromAction = formGrid.formListGrid({});
    fromAction.save = function (data) {
        var savedata = {};
        savedata.sumy = JSON.stringify(Web.getDataBySel(['#ipt_group_mk','#ipt_group_desc']));
        savedata.dtl = JSON.stringify(data);
        Web.post(Web._baseurl+'admin/menu/save.html',savedata);
        return true;
    };
    function iconLister(obj) {
        var url = obj.val();
        var td = obj.parents('td');
        if(url){
            if(td.find('.icon_pref').length > 0) td.find('.icon_pref').remove();
            var xhtml = url.indexOf('/') > -1? '<img src="'+url+'" class="icon_pref">':'<i class="'+url+' icon_pref"></i>';
            td.find('.input-group-addon').html(xhtml);
        }
    }
    // 新增
    fromAction.afterAddRow = function (obj) {
        var dom = obj.find('[name="icon"]');
        dom.off('click').blur(function () {
            iconLister($(this));
        });
    };
    // 图标监听器
    $('[name="icon"]').blur(function () {
        iconLister($(this));
    });
});
