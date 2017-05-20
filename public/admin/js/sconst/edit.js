/**
 * Created by Administrator on 2017/5/20 0020.
 */
$(function () {
    var formGrid = new FormListener('.js__form');
    var fromAction = formGrid.formListGrid({});
    fromAction.save = function (data) {
        var savedata = {};
        savedata.sumy = JSON.stringify(Web.getDataBySel(['#ipt_scope','#ipt_scope_desc']));
        savedata.dtl = JSON.stringify(data);
        Web.post(Web._baseurl+'admin/sconst/save.html',savedata);
        return true;
    };
});
