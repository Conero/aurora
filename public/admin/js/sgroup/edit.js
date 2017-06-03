/**
 * Created by Administrator on 2017/6/3 0003.
 */
$(function () {
    var formGrid = new FormListener('.js__form');
    var fromAction = formGrid.formListGrid({});
    fromAction.save = function (data) {
        if(Web.IsRequired('form')) return false;
        var savedata = {};
        var sumy = Web.getDataBySel(['#ipt_code','#ipt_descrip','#ipt_remark']);
        if($('[name="pk"]').length > 0){
            sumy.pk = $('[name="pk"]').val();
        }
        savedata.sumy = JSON.stringify(sumy);
        savedata.dtl = JSON.stringify(data);
        Web.post(Web._baseurl+'admin/sgroup/save.html',savedata);
        return true;
    };
});