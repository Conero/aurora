/**
 * Created by Administrator on 2017/5/17 0017.
 */
$(function () {
    var formGrid = new FormListener('.js__form');
    var fromAction = formGrid.formListGrid({});
    fromAction.save = function (data) {
        var savedata = {};
        savedata.sumy = JSON.stringify(Web.getDataBySel(['#ipt_group_mk','#ipt_group_desc']));
        savedata.dtl = JSON.stringify(data);
        Web.post(Web._baseurl+'admin/menu/save.html',savedata);
        return true;
    };
});
