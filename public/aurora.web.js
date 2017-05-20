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
        config.pk = config.pk? config.pk:'pk';

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
// bootstrap 控件
(function (obj) {
    // option = [title,id+,content,footer,header+]
    // 内嵌式模板窗口生成器- fn - model(show/hide)
    obj.modal = function(option,btpOpt,fn){
        if(obj.is_string(option)){
            var having = $(option).length > 0? true:false;
            if(having == true){
                $(option).modal(btpOpt);
            }
            return having;
        }
        option = typeof(option) == 'undefined'?{}:option;
        var title = obj.empty(option.title)? '模式窗口':option.title;
        var id = obj.empty(option.id)? 'page_modal':option.id;
        var content = obj.empty(option.content)? '模式内容':option.content;
        var header = obj.empty(option.header)? '':option.header;
        var footer = obj.empty(option.footer)? '':option.footer;
        var largeSize = obj.empty(option.large)? '':' modal-lg'; // 支持控制
        var saveBtn = !obj.empty(option.save) && obj.is_function(option.save)? true:false;
        var container = '<div class="modal fade" id="'+id+'" tabindex="-1" role="document" aria-labelledby="myModalLabel" aria-hidden="true">';
        var html = ''
                + '<div class="modal-dialog'+largeSize+'">'
                +    '<div class="modal-content">'
                +      '<div class="modal-header">'
                +        '<h4 class="modal-title">'+title+'</h4>'
                +        '<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>'
                +        header
                +      '</div>'
                +      '<div class="modal-body">'+content+'</div>'
                +      '<div class="modal-footer">'
                +        '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>'
                +        (saveBtn? '<button type="button" class="btn btn-default" dataid="default_save">确定</button>':'')
                +        footer
                +      '</div>'
                +    '</div>'
                + '</div>'
            ;
        id = '#'+id;
        var having = $(id).length == 0? false:true;
        if(having){
            $(id).html(html);
            $(id).attr('class','modal fade');
        }
        else{
            html = container + html +'</div>';
            $('body').append(html);
        }
        if(btpOpt) $(id).modal(btpOpt);
        else $(id).modal();
        // 事件绑定处理
        if(saveBtn){
            $(id+' [dataid="default_save"]').off("click");
            $(id+' [dataid="default_save"]').on('click',option.save);// 通过[dataid]属性事件绑定
        }
        if(obj.is_object(fn)){
            if(obj.is_object(fn.bindEvent)){
                var arr = fn.bindEvent;
                for(var i=0; i<arr.length; i++){
                    $(id+' [dataid="'+arr[i]+'"]').off("click");
                    $(id+' [dataid="'+arr[i]+'"]').on('click',fn[arr[i]]);// 通过[dataid]属性事件绑定
                }
            }
            else if(obj.is_string(fn.bindEvent)){
                var dataid = fn.bindEvent;
                $(id+' [dataid="'+dataid+'"]').off('click'); // 绑定前解绑-避免重复绑定
                $(id+' [dataid="'+dataid+'"]').on('click',fn[dataid]);// 通过[dataid]属性事件绑定
            }
        }
        return $(id); // 返回模态窗 对象
    };
    // 内嵌式是alter
    obj.alert = function(el,content,title){
        // 扩展 标题 可窗口数字用于定时自动清除
        var times = isNaN(title)? 0 : title;
        if(times > 0)  title = null;
        title = obj.empty(title)? '警告':title;
        var type = 'warning';
        if(obj.is_object(content)){
            if(content['title']) title = content['title'];
            if(content['times']) times = content['times'];
            if(content.type) type = content.type;
            content = content['text'];
        }
        content = obj.empty(content)? ' 这是一个警告提示框示例！':content;
        var html = ''
                + '<div class="alert alert-'+type+' alert-dismissible fade in" role="alert">'
                + '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>'
                + '<span class="glyphicon glyphicon-question-sign"></span> <strong>'+title+'</strong> '
                + content
                + '</div>'
            ;
        // 回调函数
        if(obj.is_function(el)){
            el(html);return;
        }
        if(obj.is_string(el) && 'feek' == el.toLowerCase()) return html;// 返回字符串
        if(obj.empty(el)){// 测试示例
            $('body').append(html);return;
        }
        el = obj.is_string(el)? $(el):el;
        el.html(html);
        if(!isNaN(times) && times > 0){
            var clearAlert = function(){
                el.html('');
            };
            setTimeout(clearAlert,times*1000);
        }
    };
    // modal -alert
    obj.modal_alert = function(text,title){
        if(this.empty(text)) return;
        var title = this.empty(title)? '警告':title;
        this.modal({
            id:		'btsp_modal_alter',
            title:	'Error-CONERO@...',
            content: '<div class="alert alert-info" role="alert"><span class="glyphicon glyphicon-info-sign"></span> <strong>'+title+'!</strong> '+text+'</div>'
        });
    };
    /**
     * 动态进度条 2017年1月7日 星期六
     * option:JSON {
		 * 			id: elId		元素ID
		 * 			max: 100		最大值
		 * 			start: 0		起始值
		 * 			sec: 1s			执行相间时间/ s
		 * 			rate: (1-10) 	增加幅度 - 默认 1: 10% 百分之10的新增速度
		 * 			type: success/info/warning/danger
		 * 			html: selector/element	 元素插入值
		 * 			append: selector/element	 元素插入值
		 * }
     * close
     */
    var _pGridSIntervalId;
    obj.progressGrid = function(option,clearMk){
        option = obj.is_object(option)? option:{};
        var id = option.id? option:'btsp_dynamic_progress';
        var max = option.max? option.max : 100;
        var start = option.start? option.start : 0;
        var bar = $('#'+id);
        var type = option.type? option.type:'success';
        var rate = (option.rate && !isNaN(option.rate) && (option.rate>0 && option.rate <11))? parseInt(option.rate):1;
        rate = Math.ceil(max*rate*0.1);
        // 清除当前正在运行的定时器
        clearMk = option.close? true : clearMk;
        if(!obj.empty(clearMk)){
            if(_pGridSIntervalId) clearInterval(_pGridSIntervalId);
            if(bar.length > 0) bar.remove();		// 删除元素
            return true;
        }
        else if(_pGridSIntervalId) clearInterval(_pGridSIntervalId);
        // 生成
        var progressBar =
                '<div class="progress-bar progress-bar-'+type+' progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="'+max+'" style="width: 40%">'
                +	'<span class="sr-only">40% Complete ('+type+')</span>'
                +'</div>'
            ;
        var xhtml =
                '<div class="progress" id="'+id+'">'
                + progressBar
                +'</div>'
            ;
        var el;
        if(option.html){
            el = obj.is_object(option.html)? option.html: $(option.html);
            if(bar.length == 0) el.html(xhtml);
            else bar.html(progressBar);
        }
        else{
            if(option.append) el = obj.is_object(option.append)? option.append: $(option.append);
            else el = $('body');
            if(bar.length == 0) el.append(xhtml);
            else bar.html(progressBar);
        }
        // 定时器执行
        bar = bar.length == 0? $('#'+id):bar;
        var lineEl = bar.find('div[role="progressbar"]');
        var initValue = start;
        var dynamicFn = function(){
            if(initValue > max) initValue = start;
            lineEl.attr("aria-valuenow",initValue);
            lineEl.css({'width':initValue+'%'});
            lineEl.find('span.sr-only').text(initValue+'% Complete ('+type+')');
            initValue = initValue + rate;
        };
        var sec = option.sec && !isNaN(option.sec)? option.sec:1;
        sec = sec * 1000;
        _pGridSIntervalId = setInterval(dynamicFn,sec);   		// clearInterval() ->
        return _pGridSIntervalId;
    };
    // confirm - modal 插件确认按钮 - 文本，回调函数，标题
    obj.confirm = function(text,callback,title){
        text = text? text:'数据确认框';
        title = title? title:'Confirm-CONERO@...';
        var content = '<div class="alert alert-danger" role="alert"><strong>(^_^)</strong> '+text+'</div>';
        this.modal({
            id:	'btsp_modal_confirm',
            title:title,
            content:content,
            save:callback
        });
    };
    // prompt 绑定输入框
    obj.prompt = function(text,callback){
        text = text? text : 'prompt 输入示例';
        var content = ''
                + '<div class="has-error">'
                + '<label for="btsp_mdprompt_impt">'+text+'</label>'
                + '<input id="btsp_mdprompt_impt" class="form-control">'
                + '</div>'
            ;
        var title = "输入信息确认";
        var afterCheckEven = function(){
            var value = $('#btsp_mdprompt_impt').val();
            var defaultFn = function(){
                $('#btsp_modal_prompt').modal('hide');
            };
            if(obj.is_function(callback)) callback(value);
            defaultFn();
        };
        this.modal({
            id:	'btsp_modal_prompt',
            title:title,
            content:content,
            save : afterCheckEven
        });
    };
    /**
     *	2016年12月3日 星期六
     *	option = {post,field},fn = {serach:function(){},save:function(){},selected:function(){},next:function(){}}
     *  option = {title:'账单选择器',
		 * 			field:{use_date:'日期',finc_no:'hidden',name:'名称'},
		 * 			post:{table:'finc_set',order:'use_date desc',map:'center_id="'+Cro.uInfo.cid+'"'},
		 * 			pupopId: 控件ID - 默认为/ 表名称
		 * 			single:单选};
     **/
    obj.pupop = function(option,fn){
        option = obj.is_object(option)? option:{};
        fn = obj.is_object(fn)? fn:{};
        var post = option.post;
        var field = option.field, postField = new Array();
        var table = '',value ='';
        var mulSelected = obj.empty(option.single)? true:false;		// 多选
        var largeSize = option.largeSize;
        var id = option.pupopId? option.pupopId:post.table;	// 控件ID
        var cols = 0;
        for(var k in field){
            cols = 1 + cols;
            postField.push(k);
            value = field[k];
            table += '<th'+(value == 'hidden'? ' class="hidden"':'')+'>'+value+'</th>';
        }
        table = '<table class="table"><tr><th>#</th>'+table+'<th>选择</th></tr>';
        post.field = postField.join(',');
        // 原始条件 - 只读
        var _sourcePostMap = post.map;
        var _sourceSearchMap = _sourcePostMap;		// 固定执行的查询条件-用于翻页
        // 单项选择涉及多次绑定事件
        var mulBindSelectEvent = function(){
            // 选择事件绑定
            if(obj.is_function(fn.selected)){
                $('#'+id+' [dataid="selected"]').unbind('click'); // 绑定前解绑-避免重复绑定
                $('#'+id+' [dataid="selected"]').on('click',fn.selected);// 单选择
            }
        };
        $.post('/conero/index/common/popup.html',post,function(data){
            // 生成表格喊函数 data 数据； startRow 最大行
            var createTabel = function(newData,startRow){
                var data = newData;
                if(obj.empty(data)) return;// 不存在时不执行函数
                var result = JSON.parse(data);
                data = result.data;
                var trs = '',i = 1,td = '',isbreak;
                if(startRow){
                    i = $('#'+id).find('tr').length;
                }
                for(var k in data){
                    if(obj.is_function(data[k])) continue;// for(遍历有函数对象)
                    td = '<tr class="datarow"><td>'+i+'</td>';
                    isbreak = false;
                    for(var kk in field){
                        if(obj.empty(data[k])){
                            isbreak = true;
                            break;
                        }
                        value = field[kk];
                        td += '<td class="'+(value == 'hidden'? 'hidden':kk)+'">'+data[k][kk]+'</td>';
                    }
                    if(isbreak) break;
                    trs += td+'<td>'+((mulSelected == true)? '<input type="checkbox" name="popupchecked">':'<a href="javascript:void(0);" dataid="selected">选择</a>')+'</td></tr>';
                    //trs += td+'<td><input type="button" name="popup_checked"></td></tr>';
                    i++;
                }
                if(startRow){// 翻页时使用
                    var alertDiv = $('#'+id).find('div.alert');
                    alertDiv.find('[dataname="no"]').text(result.no);
                    alertDiv.find('[dataname="pages"]').text(result.pages);
                    var datatable = $('#'+id).find('table');
                    datatable.append(trs);
                    // 选择事件重新绑定
                    mulBindSelectEvent();
                    return trs;
                }
                var info = ' 数据条数<span dataname="count">'+result.count+'</span>,当前分页：<span dataname="no">'+result.no+'</span>/<span dataname="pages">'+result.pages+'</span>';
                var html = table+trs+
                        '</table>'+
                        '<div class="alert alert-success" role="alert">'+
                        '<a href="javascript:void(0);"><button type="button" class="btn btn-primary" dataid="nextpage">+</button></a> '+info+
                        '</div>'
                    ;
                return html;
            };
            // 搜索框
            var opts = '';
            for(var kk in field){
                if('hidden' != field[kk]) opts += '<option value="'+kk+'">'+field[kk]+'</option>'
            }
            var search = '<div class="form-inline">'+
                '<select class="form-control" name="skey">'+opts+'</select>'+
                '<input type="text" class="form-control" name="svalue">'+
                '<button type="button" class="btn btn-primary" dataid="search">查找</button>'+
                '</div>';
            var content = search + createTabel(data);
            var popup = {
                content:content,
                footer:(mulSelected == false? '':'<button type="button" class="btn btn-default" dataid="save">确认</button>'),
                id: id,
                large:(largeSize? true:null)
            };
            // 列数过长时自动切换成-大尺寸/ modal
            if(cols>3 && obj.empty(popup.large)) popup.large = true;
            if(!obj.empty(option.title)) popup.title = option.title;
            obj.modal(popup);
            // 事件绑定 回调对象
            var callSearch = true,callNext = true;
            if(obj.is_object(fn)){
                //if(obj.is_function(fn.search)) $(document).on('click','#'+id+' [dataid="search"]',fn['search']);// 搜索  $(document).on - 无法解绑，此处可能引起事件重复绑定/一次触发引发多次响应
                if(obj.is_function(fn.search)){ // 搜索
                    callSearch = false;
                    $('#'+id+' [dataid="search"]').unbind('click'); // 绑定前解绑-避免重复绑定
                    $('#'+id+' [dataid="search"]').on('click',fn.search);
                }
                /*
                 if(obj.is_function(fn.selected)){
                 $('#'+id+' [dataid="selected"]').unbind('click'); // 绑定前解绑-避免重复绑定
                 $('#'+id+' [dataid="selected"]').on('click',fn.selected);// 单选择
                 }
                 */
                mulBindSelectEvent();
                if(obj.is_function(fn.save)){
                    $('#'+id+' [dataid="save"]').unbind('click'); // 绑定前解绑-避免重复绑定
                    $('#'+id+' [dataid="save"]').on('click',fn.save);// 保存
                }
                if(obj.is_function(fn.next)){ // 数据加载
                    callNext = false;
                    $('#'+id+' [dataid="nextpage"]').unbind('click'); // 绑定前解绑-避免重复绑定
                    $('#'+id+' [dataid="nextpage"]').on('click',fn.next);
                }
            }
            // 搜索事件自动生成
            if(callSearch == true){
                $('#'+id+' [dataid="search"]').unbind('click'); // 绑定前解绑-避免重复绑定
                $('#'+id+' [dataid="search"]').on('click',function(){
                    var form = $(this).parents('div.form-inline');
                    //var datatable = $('#'+id).find('table');
                    var skey = form.find('select option:selected').val();
                    var input = form.find('input[name="svalue"]');
                    var svalue = input.val();
                    if(obj.empty(svalue)){input.focus();return;}
                    var searchPost = post;
                    var map = _sourcePostMap, wh;
                    if(obj.is_object(map)){
                        map[skey] = ['like','%'+svalue+'%'];
                        searchPost.map = map;
                    }
                    else if(obj.is_string(map) && map){
                        searchPost.map = _sourcePostMap + ' and '+skey+' like \'%'+svalue+'%\'';
                    }
                    // 从第首页开始
                    searchPost.page = 1;
                    _sourceSearchMap = searchPost.map;
                    $.post('/conero/index/common/popup.html',searchPost,function(data){
                        var html = createTabel(data);
                        var body = $('#'+id).find('div.modal-body');
                        body.find('table').remove();
                        body.find('div.alert').remove();
                        body.append(html);
                        // 选择事件重新绑定
                        mulBindSelectEvent();
                        // 翻页处理事件 - 重新更改数据记录
                        $('#'+id+' [dataid="nextpage"]').unbind('click');
                        $('#'+id+' [dataid="nextpage"]').on('click',fn.next);
                    });
                });
            }
            // 页码翻页
            if(callNext == true){
                $('#'+id+' [dataid="nextpage"]').unbind('click'); // 绑定前解绑-避免重复绑定
                // 默认翻页函数
                if(!obj.is_function(fn.next)){
                    fn.next = function(){
                        var alertDiv = $('#'+id).find('div.alert');//obj.log(alertDiv,alertDiv.find('[dataname="no"]').text(),alertDiv.find('[dataname="pages"]').text());
                        var no = parseInt(alertDiv.find('[dataname="no"]').text());
                        var pages = parseInt(alertDiv.find('[dataname="pages"]').text());
                        var page = 1;
                        var form = $('#'+id).find('div.form-inline');
                        //var datatable = $('#'+id).find('table');
                        var skey = form.find('select option:selected').val();
                        var input = form.find('input[name="svalue"]');
                        var svalue = input.val();
                        var serachPost = post;
                        /*
                         var map = post.map, wh = '';
                         if(obj.is_object(map)) map[skey] = ['like','%'+svalue+'%'];
                         else if(obj.is_string(map) && map){
                         wh = ' and '+skey+' like \'%'+svalue+'%\'';
                         }
                         serachPost.map = map + wh;map = '';
                         */
                        serachPost.map = _sourceSearchMap;
                        // map 在ajax请求错误时 会覆盖会叠加原来的值 ??

                        if(no < pages) page = no + 1;
                        else return;
                        serachPost.page = page;
                        $.post('/conero/index/common/popup.html',serachPost,function(data){
                            createTabel(data,true);
                        });
                    };
                }
                $('#'+id+' [dataid="nextpage"]').on('click',fn.next);
            }
        });
    };
}(Web));