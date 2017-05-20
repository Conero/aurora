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
        this.log = console.log;
        this.error = function(msg){
            throw new Error( "Syntax error, unrecognized expression(Conero): " + msg);
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
    // storage 对象
    fn.storage = function(engine){
        return new jutilStorage(engine);
    };
/**************************** 系统级私有函数(begin) **************************************/
    /**
     * 前端 api 简单封装
     * @param url
     * @param data
     * @param fn
     * @constructor
     */
    fn.ApiRequest = function (url,data,func) {
        var url = this._baseurl + 'api/'+url;
        $.post(url,data,func);
    };
    // PHP+js+Base64
    var _jsVar;
    fn.getJsVar = function(key){
		if(typeof AuroarJs == 'undefined') return '';// undefind 函数无效
		if(this.empty(AuroarJs)) return '';
        if(this.is_string(AuroarJs) && !this.is_object(_jsVar)){
            _jsVar = JSON.parse(Base64.decode(AuroarJs));
        }
        if(this.is_object(_jsVar)){
			if(this.undefind(key)) return _jsVar;
            if(this.empty(_jsVar[key])) return '';
            return _jsVar[key];
        }
		return '';
    };
    // 与 php bsjson 函数匹配
	fn.bsjson = function(value){
		if(value){
			// 解密并返回 对象
			if(this.is_string(value)){
				try {
					value = Base64.decode(value);
					return JSON.parse(value);
				} catch (error) {
					this.error(error);
				}
			}
			else if(this.is_object(value) && this.objectLength(value) >0){
				var str = JSON.stringify(value);
				return Base64.encode(str);
			}
		}
		return '';
	};
/**************************** 系统级私有函数(end) **************************************/
    return new aurora();
}
/**----------------------新扩展--------------------------2016年8月30日 星期二>>  				类似数据库处理操作// js面向对象式编程
 *	engine 引擎|| session/local，默认前者
 *	add()		插入数据到storage内，会覆盖历史数据
 *	update()	更新数据到storage内，支持 json/string，可实现删除内部数据
 *	select()	storage数据获取
 *	get()		简单数据获取法
 *
 *	table()		get/set storage数据键名
 *	error()		get/set storage数据异常
 *	session()/local()	storage数据设置获取函数(原型)
 *	undefind()	数据格式检测
 *	object(),empty()
 */
function jutilStorage(engine){
    if('session' != engine && 'local' != engine) this.engine = 'session';
    else this.engine = engine;
    this.table = function(tb){
        if(this.undefind(tb)){
            var table = this._table;
            if(this.undefind(table)) this._table = '';
            return this._table;
        }else{
            this._table = tb;
            return this; // 链式数据处理
        }
    };
    //	会覆盖原来的值，若存在原来的json数据/JSON
    this.add = function(data,table){
        if(this.empty(table)) table = this.table();
        if(this.empty(table)){
            this.error('add## 无法获取到table的值！');
            return '';
        }
        if(!this.object(data)){
            this.error('add## 存储数据必须为JSON格式数据！');
            return '';
        }
        var str = JSON.stringify(data);
        if(this.engine == 'session') this.session(table,str);
        else this.local(table,str);
        return true;
    };
    //	更新数据/可更新不存在的数据
    this.update = function(key,value){
        var tb = this.table();
        if(this.empty(tb)){
            this.error('select## 无法获取到table的值！');
            return false;
        }
        var data = this.select();
        if(this.empty(key)){
            this.error('update## 无法获取到key的值,请设置key（json/string）值！');
            return false;
        }
        if(!this.object(data)) data = {};
        if(this.object(key)){
            for(var k in key){data[k] = key[k];}
        }
        if(this.undefind(value)){
            delete data[key];
        }else{
            data[key] = value;
        }
        var str = JSON.stringify(data);
        return this.engine == 'session'? this.session(tb,str):this.local(tb,str);
    };
    this.select = function(key,value){
        var tb = this.table();
        if(this.empty(tb)){
            this.error('select## 无法获取到table的值！');
            return '';
        }
        var str = this.engine == 'session'? this.session(tb):this.local(tb);
        try{
            var data = JSON.parse(str);
        }catch(e){
            this.error(e);
            var data = {};
        }
        if(this.empty(key)) return data;//	返回整个json数据
        else if(this.undefind(value)){//	返回单个json数据
            if(!this.empty(data) && data[key]) return data[key];
            return '';
        }
        data[key] = value;//	设置json的属性值
    };
    // 分隔符解析数组
    this.array = function(key,value,delimiter){
        if(this.empty(key)) return;
        delimiter = delimiter? delimiter:',';
        if(this.undefind(value)){// 获取值
            var tmp = this.get(key);
            if(tmp) return tmp.split(delimiter);
            return;
        }
        if(this.empty(value)) return;
        var tmp = this.get(key);
        if(tmp.indexOf(value) == -1){
            var arr = tmp.split(delimiter);
            arr.push(value);
            this.update(key,arr.join(delimiter));
        }
    };

    // 函数分隔符数组中指定的属性值
    this.removeArray = function(key,value,delimiter){
        var arr = this.array(key);
        var newArr = new Array();
        for(var i=0; i<arr.length; i++){
            if(arr[i] == value) continue;
            newArr.push(arr[i]);
        }
        delimiter = delimiter? delimiter:',';
        return this.update(key,newArr.join(delimiter));
    };
    this.get = function(key){
        return this.select(key);
    };
    this.undefind = function(value){
        if(typeof(value) == 'undefind') return true;
        else if(value == null) return true;
        return false;
    };
    this.object = function(data){
        if(null == data) return false;
        if(typeof(data) == 'object') return true;
        return false;
    };
    this.empty = function(value){
        if(this.undefind(value)) return true;
        else if(value == '') return true;
        else if(value == 0) return true;
        return false;
    };
    this.session = function(name,value)
    {
        if(this.undefind(window.sessionStorage)){this.error('浏览器不支持 sessionStorage');}
        if(this.empty(name)) return null;
        if(this.empty(value))	return sessionStorage.getItem(name);
        sessionStorage.setItem(name,value);
        return true;
    };
    //loaclstroge 本地存储	2016/4/8
    this.local = function(name,value)
    {
        if(this.undefind(window.localStorage)){this.error('浏览器不支持 localStorage');}
        if(this.empty(name)) return false;
        if(this.empty(value))	return localStorage.getItem(name);
        localStorage.setItem(name,value);
        return true;
    };
    // 删除storage
    this.delete = function(tb){
        tb = tb || this._table;
        if(this.empty(tb)) return false;
        if(engine == 'local'){
            if(localStorage.getItem(tb)){
                localStorage.removeItem(tb);
                return true;
            }
        }
        else if(sessionStorage.getItem(td)){
            sessionStorage.removeItem(td);
            return true;
        }
        return false;
    };
    this.error = function(err){
        if(this.undefind(err)){
            var message = this._error;
            if(this.undefind(message)) this._error = '0';
            //	浏览器自动调试输出
            try{console.log(this._error)}catch(e){}
            return this._error;
        }else this._error = err;
    };
    this.is_string = function(value){
        if(typeof value == 'string') return true;
        return flase;
    };
    this.is_object = function(value){
        if(typeof value == 'object') return true;
        return flase;
    }
}

var Base64 = {
    // private property
    _keyStr: "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",

    // public method for encoding
    encode: function (input) {
        var output = "";
        var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
        var i = 0;
        input = this._utf8_encode(input);
        while (i < input.length) {
            chr1 = input.charCodeAt(i++);
            chr2 = input.charCodeAt(i++);
            chr3 = input.charCodeAt(i++);
            enc1 = chr1 >> 2;
            enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
            enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
            enc4 = chr3 & 63;
            if (isNaN(chr2)) {
                enc3 = enc4 = 64;
            } else if (isNaN(chr3)) {
                enc4 = 64;
            }
            output = output +
                this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
                this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);
        }
        return output;
    },

    // public method for decoding
    decode: function (input) {
        var output = "";
        var chr1, chr2, chr3;
        var enc1, enc2, enc3, enc4;
        var i = 0;
        input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");
        while (i < input.length) {
            enc1 = this._keyStr.indexOf(input.charAt(i++));
            enc2 = this._keyStr.indexOf(input.charAt(i++));
            enc3 = this._keyStr.indexOf(input.charAt(i++));
            enc4 = this._keyStr.indexOf(input.charAt(i++));
            chr1 = (enc1 << 2) | (enc2 >> 4);
            chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
            chr3 = ((enc3 & 3) << 6) | enc4;
            output = output + String.fromCharCode(chr1);
            if (enc3 != 64) {
                output = output + String.fromCharCode(chr2);
            }
            if (enc4 != 64) {
                output = output + String.fromCharCode(chr3);
            }
        }
        output = this._utf8_decode(output);
        return output;
    },

    // private method for UTF-8 encoding
    _utf8_encode:function (string) {
        string = string.replace(/\r\n/g,"\n");
        var utftext = "";
        for (var n = 0; n < string.length; n++) {
            var c = string.charCodeAt(n);
            if (c < 128) {
                utftext += String.fromCharCode(c);
            } else if((c > 127) && (c < 2048)) {
                utftext += String.fromCharCode((c >> 6) | 192);
                utftext += String.fromCharCode((c & 63) | 128);
            } else {
                utftext += String.fromCharCode((c >> 12) | 224);
                utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                utftext += String.fromCharCode((c & 63) | 128);
            }

        }
        return utftext;
    },

    // private method for UTF-8 decoding
    _utf8_decode:function (utftext) {
        var string = "";
        var i = 0;
        var c = c1 = c2 = 0;
        while ( i < utftext.length ) {
            c = utftext.charCodeAt(i);
            if (c < 128) {
                string += String.fromCharCode(c);
                i++;
            } else if((c > 191) && (c < 224)) {
                c2 = utftext.charCodeAt(i+1);
                string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                i += 2;
            } else {
                c2 = utftext.charCodeAt(i+1);
                c3 = utftext.charCodeAt(i+2);
                string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                i += 3;
            }
        }
        return string;
    }
};


