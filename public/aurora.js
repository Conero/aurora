/**
 * Created by Administrator on 2017/5/7 0007.
 * 系统框架
 * 选择：[wap,web]
 */
// 时间原型
Date.prototype.sysdate = function(format){
    format = format? format:"y-m-d h:i:s";
    var fullYear = this.getFullYear();
    var M = this.getMonth()+1;
    var D = this.getDate();
    var H = this.getHours();
    var I = this.getMinutes();
    var S = this.getSeconds();
    var sorce = {
        'y':fullYear,'Y':parseInt((fullYear).toString().slice(2))
        ,'m':(M<10? '0'+M:M),'M':M
        ,'d':(D<10? '0'+D:D),'D':D
        ,'h':(H<10? '0'+H:H),'H':H
        ,'i':(I<10? '0'+I:I),'I':I
        ,'s':(S<10? '0'+S:S),'S':S
        ,'w':this.getDay()
        ,'ms':this.getMilliseconds()
    };
    if(format.indexOf("ms") > -1) format = format.replace(new RegExp('ms','g'),sorce['ms']);
    for(var k in sorce){
        if(format.indexOf(k) == -1 || k == "ms") continue;
        if(format.indexOf(k) > -1) format = format.replace(new RegExp(k,'g'),sorce[k]);
    }
    return format;
};
function Aurora() {
    // 内部函数 - 对象原型
    function aurora() {
        this._baseurl = '/aurora/';
        this.is_string = function(value){
            if(typeof(value) == 'string') return true;
            return false;
        };
        this.is_object = function(value){
            if(typeof(value) == 'object') return true;
            return false;
        };
        this.is_array = Array.isArray;
        // 查看array/json 是否存在值
        this.inArray = function(key,arr){
            var ret = false;
            if(key && this.is_object(arr)){
                if(this.is_array(arr)){
                    for(var k=0; k<arr.length; k++){
                        if(arr[k] == key) return true;
                    }
                }
                else{
                    for(var k in arr){
                        if(arr[k] == key) return true;
                    }
                }
            }
            return false;
        };
        // 求数组或json的长度
        this.objectLength = function(value){
            var len = 0;
            // 对象长度
            if(this.is_object(value)){
                if(this.is_array(value)) return value.length;
                // json 通过遍历获取
                for(var k in value){
                    len = len + 1;
                }
                return len;
            }
            // 其他具有length 属性的对象
            else if(value && value.length) return value.length;
            return len;
        };
        this.is_function = function(value){
            if(typeof(value) == 'function') return true;
            return false;
        };
        this.is_number = function(value){
            var peg = /^[0-9]+$/;
            if(value){
                value = value.replace(/\s/g,''); //  删除空格
                return peg.test(value)
            }
            return false;
        };
        this.undefind = function(value){
            if(typeof(value) == 'undefined') return true;
            return false;
        };
        this.empty = function(value){
            if(this.undefind(value)) return true;
            else if(value == '') return true;
            // else if(value == 0) return true; // "00" 无法通过
            else if(value == null) return true;
            return false;
        };
        // js/模拟保单----------------------------------------------------------------------->
        this.form = function(url,data,method){
            if(this.empty(url) || this.empty(data)) return false;
            if(this.is_string(data)){
                try {
                    data = JSON.parse(data);
                } catch (error) {
                    return error;
                }
            }
            if(method) method = method.toLowerCase();
            method = this.empty(method)? 'post':method;
            if(method && (method != "post" && method != "get")) method = "post";
            var form = document.createElement("form");
            form.action = url;
            form.method = method;
            form.style = "display:none;";
            var ipt;
            if($.isArray(data)){// Array 对象 - 包含 - prototype新增的扩展对象 last/unset
                for(var k=0; k<data.length; k++){
                    ipt = document.createElement("textarea");
                    ipt.name = k;
                    ipt.value = data[k];
                    form.appendChild(ipt);
                }
            }
            else{
                for(var k in data){
                    ipt = document.createElement("textarea");
                    ipt.name = k;
                    ipt.value = data[k];
                    form.appendChild(ipt);
                }
            }
            document.body.appendChild(form);
            form.submit();
            return form;
        };
        this.post = function(url,data){return this.form(url,data,"post");};
        this.get = function(url,data){return this.form(url,data,"get");};
        // js/模拟保单	<-----------------------------------------------------------------------
        // 获取formJson - 选择器下元素的值
        this.formJson = function(selector){
            var el = this.is_object(selector)? selector:$(selector);
            if(el.length>0){
                var saveData = {};
                // input
                var ipts = el.find("input");
                var El,key,i=0;
                for(i=0; i<ipts.length; i++){
                    El = $(ipts[i]);
                    if(El.attr('disabled')) continue; // 忽略禁用元素
                    if(El.attr('type') == 'checkbox' && !El.is(':checked')) continue;// 忽略未被选中的复选框
                    if(El.attr('type') == 'radio' && !El.is(':checked')) continue;// 忽略未被选中的单选框
                    key = El.attr("name");
                    if(this.empty(key)) continue;
                    saveData[key] = El.val();
                }
                // textarea
                ipts = el.find('textarea');
                for(i=0; i<ipts.length; i++){
                    El = $(ipts[i]);
                    key = El.attr("name");
                    if(this.empty(key)) continue;
                    saveData[key] = El.val();
                }
                // select
                var sels = el.find("select");
                for(i=0; i<sels.length; i++){
                    El = $(sels[i]);
                    key = El.attr("name");
                    if(this.empty(key)) continue;
                    saveData[key] = El.find('option:selected').val();
                }
                return saveData;
            }
            return null;
        };
        /**
         * required 依然有效，即requird 要求的不符合要求
         * @constructor
         */
        this.IsRequired = function (selector) {
            var els = selector? $(selector).find('[required]'):$('[required]');
            for(var i=0; i<els.length; i++){
                var el = $(els[i]);
                if(this.formVal(el) == '') return true;
            }
            return false;
        };
        // 表单有效值
        this.formVal = function (selector) {
            var el = this.is_object(selector)? selector:$(selector);
            var value = '';
            if(el.is('input')) {
                if (el.attr('type') == 'checkbox' && !el.is(':checked')) value = '';// 忽略未被选中的复选框
                else if (el.attr('type') == 'radio' && !el.is(':checked')) value = '';// 忽略未被选中的单选框
                else value = el.val();
            }else if(el.is('select')){
                value = el.find('option:selected').val();
            }else
                value = el.val();
            return value;
        };
        /**
         * 后去保存数据, 通过 js__col 保存的, 键值 [name/col-name]
         * @param selector
         */
        this.getSaveData = function (selector) {
            var savedata = {};
            // 作用域
            var cols = selector? $(selector).find('.js__col'): $('.js__col');
            for(var i=0; i<cols.length; i++){
                var el = $(cols[i]);
                if(el.attr('disabled')) continue; // 忽略禁用元素
                var key = el.attr("name");
                key = key? key: el.attr('col-name');
                if(this.empty(key)) continue;
                if(el.is('input')) {
                    if (el.attr('type') == 'checkbox' && !el.is(':checked')) continue;// 忽略未被选中的复选框
                    if (el.attr('type') == 'radio' && !el.is(':checked')) continue;// 忽略未被选中的单选框
                    savedata[key] = el.val();
                }else if(el.is('select')){
                    var value = el.find('option:selected').val();
                    savedata[key] = value;
                }else
                    savedata[key] = el.val();
            }
            return savedata;
        };
    }
    var fn = aurora.prototype;
    /******************** 移动端与客服端公共函数 *******************************************/

    // console.log 集成化处理
    if(console && console.log) fn.log = console.log;

    /**
     * getQuery 获取 json
     * @param skey 键值
     * @return {json/string}
     */
    function getQuery(skey) {
        var json = {};
        var queryStr = location.search;
        if(queryStr.length>0){
            queryStr = queryStr.substr(1);
            var tmpArray = queryStr.split('&');var key,value,idx;
            for(var k=0;k<tmpArray.length;k++){
                value = tmpArray[k];
                idx = value.indexOf('=');
                // 删除非法字符
                if(idx == -1) continue;
                key = value.substr(0,idx);
                value = value.substr(idx+1);
                if(skey && key == skey) return value;   // 获取单值
                json[key] = value;
            }
        }
        if(skey) return "";
        return json;
    }
    /**
     * @name 获取 url-中query的值 如： ?k1=v1&k3=v3 => its100a.getQuery('k1') => 'v1'
     */
    fn.getQuery = function(key){
        var strKey = key+'=';
        var href = location.href;
        var idx = href.indexOf(strKey,href);
        if(idx == -1) return '';
        var tmpStr = href.substr(idx);
        if(tmpStr.indexOf('&',tmpStr) > -1) tmpStr = tmpStr.substr(0,tmpStr.indexOf('&',tmpStr));
        tmpStr = tmpStr.substr(tmpStr.indexOf('=',tmpStr)+1);
        return tmpStr;
    };
    fn.urlGet = function (key) {
        return getQuery(key);
    };
    /**
     * js json转化get请求参数
     * @param json
     * @returns {string}
     */
    fn.parseQuery = function (json) {
        var queryString = '';
        var tmpArr = [];
        for(var k in json){
            tmpArr.push(k+'='+json[k]);
        }
        if(tmpArr.length>0) queryString = '?'+tmpArr.join('&');
        return queryString;
    };
    fn.updateQuery = function (json,url) {
        json = fn.JsonMerge(getQuery(),json);
        var querystr = fn.parseQuery(json);
        url = url? url:(location.origin+location.pathname+querystr+location.hash);
        return url;
    };
    /**
     * array 合并
     * @param array ...
     */
    fn.ArrayMerge = function () {
        if(arguments.length < 2) return [];
        var baseArray = arguments[0];
        var tmpArray = [];
        for(var k=1; k<arguments.length; k++){
            tmpArray = arguments[k];
            if(typeof(tmpArray) == 'object' && tmpArray.length > 0) {
                for (var kk = 0; kk < tmpArray.length; kk++) {
                    baseArray.push(tmpArray[kk]);
                }
            }
        }
        return baseArray;
    };
    /**
     * json 合并
     * @constructor
     */
    fn.JsonMerge = function () {
        if(arguments.length < 2) return [];
        var baseJson = arguments[0]; var tmpJson;
        baseJson = (typeof baseJson == 'object' && typeof (baseJson.length) == 'undefined')? baseJson:{};
        for(var k=1;k<arguments.length;k++){
            tmpJson = arguments[k];
            if(typeof(tmpJson) == 'object' && typeof(tmpJson.length) == 'undefined'){
                for(var kk in tmpJson){
                    baseJson[kk] = tmpJson[kk];
                }
            }
        }
        return baseJson;
    };
    /**
     * json 数据清洗 -> 根据keys帅选数据
     * @param json
     * @param keys string/[]string
     * @param notnull 是否清楚其中的空值->
     * @constructor
     */
    fn.JsonClear = function (json,keys,notnull) {
        // 去除空值
        if(notnull){
            var tmpJson = {};
            for(var k in json){
                if('' == json[k] || null == json[k]) continue;
                tmpJson[k] = json[k];
            }
            json = tmpJson;
        }
        if($.isArray(keys)){
            for(var i=0; i<keys.length; i++){
                var k = keys[i];
                if(typeof json[k] != 'undefined') delete json[k];
            }
        }else if(typeof json[keys] != 'undefined'){
            delete json[keys];
        }
        return json;
    };
    return new aurora();
}


