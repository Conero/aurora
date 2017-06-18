/**
 * Created by Administrator on 2017/6/10 0010.
 */
$(function () {
    // js 参考时间
   $('.js__ref').click(function () {
       var dom = $(this);
       var ref = dom.attr('data-for');
       if(ref != ''){
           $('#'+ref).val(dom.text());
       }
   });
    // 邮箱监听器
    $('#email_ipt').blur(function () {
        var dom = $(this);
        var email = dom.val();
        if(!Validate.isEmail(email)) return Wap.WeuiFromCheck(dom,'W');
        if(email != ''){
            Wap.ApiRequest('user/col_exist',{col:'email',val:email},function (data) {
                var type = data.exist == 1? 'W':'S';
                Wap.WeuiFromCheck(dom,type);
            });
        }
    });
    // 手机号码监听器
    $('#phone_ipt').blur(function () {
        var dom = $(this);
        var phone = dom.val();
        if(!Validate.isPhone(phone)) return Wap.WeuiFromCheck(dom,'W');
        if(phone != ''){
            Wap.ApiRequest('user/col_exist',{col:'phone',val:phone},function (data) {
                var type = data.exist == 1? 'W':'S';
                Wap.WeuiFromCheck(dom,type);
            });
        }
    });
    // 表单保存
    weui.form.checkIfBlur('.js__form');
    $('#submit_lnk').click(function () {
        weui.form.validate('.js__form', function (error) {
            if (!error) {
                var loading = weui.loading('数据保存中...');
                Wap.ApiRequest('user/save', Wap.formJson('.js__form'), function (data) {
                    loading.hide();
                    weui.toast(data.msg);
                });
            }
        });
    });
});