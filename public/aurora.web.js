/**
 * Created by Administrator on 2017/5/7 0007.
 * 桌面端前端函数
 */
var Web = Aurora();

/**
 * 表单监听器 - 2017年5月17日 星期三 / 包含 列表表单处理器(json)和独立form处理器([]json)
 * juqery v3.2.1
 * @param selector 选择器
 * @constructor
 */
function FormListener(selector){
    // 不定参数
    var container = selector? $(selector):$('form');
    /**
     * 列表表单监听器, 主键值自动绑定在tr中的 data-id 属性中
     * @param config json{
     *      AddBtnEl/DelBtnEl 新增删除选择器默认： '#row_add_btn'/'#row_del_btn'
     * }
     * @return TbGrid
     */
    this.formListGrid = function (config) {
        var parentObj = this;
        var stackDelList = [];  // 删除数据记录堆栈
        var formAction = {};
        var trBindPkAttr = 'data-id';   // 绑定id
        /**
         * 获取列表长度
         * @returns {number}
         */
        formAction.getRowLen = function () {
            var len = container.find('tr').length;
            return (len-1);
        };
        /**
         * 获取指定行的 jquery对象
         * @param index
         * @return jquery
         */
        formAction.getRowObj = function (index) {
            index = index? index:this.getRowLen();
            return container.find('tr:eq('+index+')');
        };
        /**
         * 新增行
         * @return jquery
         */
        formAction.addRow = function () {
            var xhtml = container.find('tr:eq('+this.getRowLen()+')').html();
            console.log(this.getRowLen(),container.find('tr:eq('+this.getRowLen()+')',xhtml));
            var trXhtml = '<tr>'+xhtml+'</tr>';
            container.append(trXhtml);
            var trObj = container.find('tr:eq('+this.getRowLen()+')');
            var orderTd = trObj.find('td[data-no]');
            var newLen = this.getRowLen();
            if(orderTd.length > 0){
                orderTd.attr('data-no',newLen);
                orderTd.html(newLen);
            }
            parentObj.ResetForm(trObj);
            return trObj;
        };
        /**
         * 删除行
         */
        formAction.delRow = function () {
            var len = this.getRowLen();
            if(len>0){
                var trObj = container.find('tr:eq('+len+')');
                var pkValue = trObj.attr(trBindPkAttr);
                if(pkValue) stackDelList.push({type:'D',uid:pkValue});
                trObj.remove();
            }
        };
        config = config? config:{};
        var AddBtnEl = config.AddBtn? $(config.AddBtn):$('#row_add_btn');
        var DelBtnEl = config.DelBtn? $(config.DelBtn):$('#row_del_btn');
        if(AddBtnEl.length > 0){
            AddBtnEl.click(function () {
                formAction.addRow();
            });
        }
        if(DelBtnEl.length > 0){
            DelBtnEl.click(function () {
                formAction.delRow();
            });
        }
        /**
         * 获取保存至
         * @return json
         */
        formAction.getSaveData = function () {
            var savedata = stackDelList;
            var len = this.getRowLen();
            for(var i=1; i<=len; i++){
                savedata.push(parentObj.getSaveData(this.getRowObj(i)));
            }
            return savedata;
        };
        return formAction;
    };
    /**
     * 获取保存的数据 2017年5月17日 星期三
     * @param el string|jquery 选择器获取jquery对象
     * @param required bool 是否进行非空判别，不符合条件时返回空
     * @return json|null
     */
    this.getSaveData = function (el,required) {
        if(typeof el == 'string') el = $(el);
        el = typeof el == 'object'? el:container;
        if((typeof el != 'object' ) || el.length == 0) return null;
        var data = {};
        // input
        var inputs = el.find('input');
        for(var i=0; i<inputs.length; i++){
            var ipEl = $(inputs[i]);
            var key = ipEl.attr('name');
            var value = '';
            if(key == '') continue;
            var type = ipEl.attr('type');
            // 如下类型的元素不支持重置
            if(type == 'checkbox' || type == 'radio'){
                if(ipEl.is(':checked')) value = ipEl.val();
                else continue;
            }
            else value = ipEl.val();
            data[key] = value;
        }
        // textarea
        var textareas = el.find('textarea');
        for(var j=0; j<textareas.length; j++){
            var taEl = $(textareas[i]);
            var taKey = taEl.attr('name');
            if(taKey == '') continue;
            data[taKey] = taEl.val();
        }
        // select
        var selects = el.find('select');
        for(var x=0; x<selects.length; x++){
            var sEl = $(selects[i]);
            var sKey = taEl.attr('name');
            if(taKey == '') continue;
            var svalue = sEl.find('option:selected').val();
            data[sKey] = svalue;
        }
        return data;
    };
    /**
     * 重置表单数据
     * @param el string|jquery
     */
    this.ResetForm = function (el) {
        if(typeof el == 'string') el = $(el);
        el = typeof el == 'object'? el:container;
        // input
        var inputs = el.find('input');
        for(var i=0; i<inputs.length; i++){
            var el = $(inputs[i]);
            var type = el.attr('type');
            // 如下类型的元素不支持重置
            if(type == 'checkbox' || type == 'radio' || type == 'button') continue;
            el.val('');
        }
        // textarea
        var textareas = el.find('textarea');
        for(var j=0; j<textareas.length; j++){
            $(textareas[j]).val('');
        }
    };
}