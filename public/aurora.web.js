/**
 * Created by Administrator on 2017/5/7 0007.
 * 桌面端前端函数
 */
var Web = Aurora();
/**
 * 获取单个表单的值，扩展 $.val 函数使 input/select/textare 等透明
 * @param selector string|jquery
 * @param isJson 是否返回 k-v json 值
 * @return json|string
 */
Web.formValue = function (selector,isJson) {
    var el = (typeof selector == 'object')? selector:$(selector);
    var value = '';
    if(el.is('input')){
        var type = el.attr('type');
        if(type == 'checkbox' || type == 'radio') {
            if (el.is(':checked')) value = el.val();
        }
        else value = el.val();
    }
    else if(el.is('textarea')) value = el.val();
    else if(el.is('select')){
        value = el.find('option:selected').val();
    }
    if(isJson){
        var name = el.attr('name');
        var tmpJson = {};
        tmpJson[name] = value;
        return tmpJson;
    }
    return value;
};
/**
 * 通过选择器获取表单值
 * @param selector []string|string *
 * @return json
 */
Web.getDataBySel = function (selector) {
    selector = (typeof selector == 'object')? selector:[selector];
    var saveData = {};
    for(var i=0; i<selector.length; i++){
        var el = $(selector[i]);
        var name = el.attr("name");
        if(name == '') continue;
        saveData[name] = this.formValue(el);
    }
    return saveData;
};
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
     *      AddBtn/DelBtn/SaveBtn 新增删除选择器默认： '#row_add_btn'/'#row_del_btn'/'#rows_save_btn',
     *     CcopyFromTr: string|string[]  复制一行出现的值
     *     pk: 主键名称
     * }
     * @return TbGrid
     */
    this.formListGrid = function (config) {
        var parentObj = this;
        var stackDelList = [];  // 删除数据记录堆栈
        var formAction = {};
        var trBindPkAttr = 'data-id';   // 绑定id
        // 参数处理
        config = config? config:{};
        config.pk = config.pk? config.pk:'uid';

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
            var trXhtml = '<tr>'+xhtml+'</tr>';
            container.append(trXhtml);
            var trObj = container.find('tr:eq('+this.getRowLen()+')');
            var orderTd = trObj.find('td[data-no]');
            var newLen = this.getRowLen();
            if(orderTd.length > 0){
                orderTd.attr('data-no',newLen);
                orderTd.html(newLen);
            }
            if(config.CcopyFromTr) parentObj.ResetForm(trObj,config.CcopyFromTr);
            else parentObj.ResetForm(trObj);
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
                if(pkValue){
                    var savedata = {type:'D'};
                    savedata[config.pk] = pkValue;
                    stackDelList.push(savedata);
                }
                trObj.remove();
            }
        };
        /**
         * 获取数据标识
         * @param trObj jquery
         * @param key string|null
         * @return json|string|null
         */
        formAction.getPk = function (trObj,key) {
            var pk = trObj.attr(trBindPkAttr);
            if(pk){
                if(key){
                    var tmpJson = {};
                    tmpJson[key] = pk;
                    return tmpJson;
                }
                return pk;
            }
            return null;
        };
        /**
         * 获取保存至
         * @return json
         */
        formAction.getSaveData = function () {
            var savedata = [];
            var len = this.getRowLen();
            var pkName = config.pk;
            for(var i=1; i<=len; i++){
                var trObj = this.getRowObj(i);
                var trData = parentObj.getSaveData(trObj);
                var pkValue = this.getPk(trObj);
                if(pkValue) trData[pkName] = pkValue;
                savedata.push(trData);
            }
            return savedata;
        };
        // 数据保存接口
        formAction.save = function (savedata) {
        };

        var AddBtnEl = config.AddBtn? $(config.AddBtn):$('#row_add_btn');
        var DelBtnEl = config.DelBtn? $(config.DelBtn):$('#row_del_btn');
        var SaveBtnEl = config.SaveBtn? $(config.SaveBtn):$('#rows_save_btn');  // 数据保存
        // 列新增
        if(AddBtnEl.length > 0){
            AddBtnEl.click(function () {
                formAction.addRow();
            });
        }
        // 列删除
        if(DelBtnEl.length > 0){
            DelBtnEl.click(function () {
                formAction.delRow();
            });
        }
        // 数据保存
        if(SaveBtnEl.length > 0){
            SaveBtnEl.click(function () {
                var savedata = formAction.getSaveData();
                // 将删除的值写到记录中
                if(stackDelList.length > 0){
                    for(var i=0;i<stackDelList.length; i++){
                        savedata.push(stackDelList[i]);
                    }
                }
                var isBreak = formAction.save(savedata);
                if(isBreak) return false;
            });
        }

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
            data[sKey] = sEl.find('option:selected').val();
        }
        return data;
    };
    /**
     * 重置表单数据
     * @param el string|jquery
     * @param ignore string|[]string 忽略值列表
     */
    this.ResetForm = function (el,ignore) {
        if(typeof el == 'string') el = $(el);
        el = typeof el == 'object'? el:container;
        if(ignore){
            ignore = (typeof ignore == 'object')? ignore:[ignore];
        }
        // input
        var inputs = el.find('input');
        for(var i=0; i<inputs.length; i++){
            var iptEl = $(inputs[i]);
            if(ignore){
                var name = iptEl.attr("name");
                if(name && $.inArray(name,ignore)>-1) continue;
            }
            var type = iptEl.attr('type');
            // 如下类型的元素不支持重置
            if(type == 'checkbox' || type == 'radio' || type == 'button') continue;
            iptEl.val('');
        }
        // textarea
        var textareas = el.find('textarea');
        for(var j=0; j<textareas.length; j++){
            var txtEl = $(textareas[j]);
            if(ignore){
                var txtName = txtEl.attr("name");
                if(txtName && $.inArray(txtName,ignore)>-1) continue;
            }
            txtEl.val('');
        }
    };
}