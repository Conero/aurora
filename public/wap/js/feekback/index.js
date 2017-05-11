/**
 * Created by Administrator on 2017/5/11 0011.
 * 系统反馈
 */
$(function () {
    var son_key = 'feek_unlike_or';
    // 喜欢与不喜欢反馈
    $('.js__fk_lnk').click(function () {
        var has = Wap.storage().session(son_key);
        if(has != ""){
            weui.alert("谢谢您的反馈，请不要重复统计！");
            return;
        }
        var value = $(this).attr("data-value");
        Wap.ApiRequest('feek/survey',{'data':value,'type':'support_or_not'},function (data) {
           if(data.code == 1){
               weui.toast("谢谢您的反馈");
               return;
           }
            weui.toast(data.msg);
        });
    });
    // 分数
    var score = 0;
    weui.slider('#score_slider',{
        step: 10,
            defaultValue: 0,
            onChange: function(percent){
                score = Math.ceil(percent);
                $('#sliderValue').text(score);
                $('#sliderTrack').css({'width': percent+'%'});
                $('#sliderHandler').css({'left': percent+'%'});
            }
    });
});
