/**
 * Created by Administrator on 2017/5/7 0007.
 * 移动端前端函数
 */
var Wap = Aurora();
// 首页
Wap._homeUrl = Wap._baseurl+'wap.html';
// 表单检测标红与清除
Wap.CellWarning = function (dom,clear) {
    var cell = dom.parent('div.weui-cell');
    var cellFt = cell.find('.weui-cell__ft');
    if(clear){
        // 清除警告
        cell.removeClass('weui-cell_warn');
        if(cellFt.length>0) cellFt.remove();
    }else{
        cell.addClass('weui-cell_warn');
        if(cellFt.length == 0){
            var xhtml = '<div class="weui-cell__ft"><i class="weui-icon-warn"></i></div>';
            cell.after(xhtml);
        }
    }
};