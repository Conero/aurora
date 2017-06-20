/**
 * Created by Administrator on 2017/5/7 0007.
 * 移动端前端函数
 */
var Wap = Aurora();

Wap._homeUrl = Wap._baseurl+'wap.html';     // 首页
Wap._wapurl = Wap._baseurl + 'wap/';        //  wap 基础页面


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
/**
 * weui from 表单检测
 * @param dom
 * @param type   C/W/S -> clear,warning,success
 * @returns {boolean}
 * @constructor
 */
Wap.WeuiFromCheck = function (dom,type) {
    var cell = dom.parents('.weui-cell');
    var cellFt = cell.find('.weui-cell__ft');
    if(type == 'C'){
        if(cellFt.length > 0){
            cellFt.remove();
            return true;
        }
        return false;
    }
    var xhtml = '';
    if (type == 'W') {
        xhtml = '<i class="fa fa-warning text-warning"></i>';
    } else xhtml = '<i class="fa fa-check text-success"></i>';
    if (cellFt.length > 0) cellFt.html(xhtml);
    else cell.append('<div class="weui-cell__ft">' + xhtml + '</div>');
};
/**
 * 有滑动时返回，需要引入 zepto/touch.js
 * @param url 调整地址  string/function
 * @param callback 回调函数 function
 */
Wap.SwipeRightBack = function (url,callback) {
    if(typeof url == 'function'){
        callback = url;
        url = null;
    }
    url = url? url:'finance.html';
    // 右滑动时返回
    $(document).swipeRight(function () {
        if(typeof callback == 'function'){
            if(!callback()) return;
        }
        location.href = Wap._wapurl + url;
    });
};