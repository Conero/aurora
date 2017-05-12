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
/**
 * 操作成功是返回信息
 * @param msg
 * @param title 标题 可选
 * @param url 返回地址可选, 服从 urlBuild生成规则
 */
Wap.msg_success = function (msg,title,url) {
    var jsondata = {desc:msg};
    if(title) jsondata.title = title;
    if(url) jsondata.url = url;
    this.post(this._baseurl+'wap/msg/succs.html',jsondata);
};