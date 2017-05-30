/**
 * Created by Administrator on 2017/5/30 0030.
 */
$(function () {
    var item = Web.getUrlBind('item');
   // 文章 star
    $('#atc_star_lnk').click(function () {
        var starMk = $(this).attr("data-star");
        if(starMk == 'Y') return;
        Web.ApiRequest('article/star',{uid:item},function (data) {
            Web.modal_alert(data.msg);
        });
    });
    // 评论对话框
    function writeCommentDs() {
        var id = 'write_cmt_ds';
        var content = '';
        content += Web.formGroup([
            {label:'评语',name:'comment',type:'tarea',notnull:true},
            {label:'署名',name:'sign',def:'匿名'}
        ]);
        Web.modal({
            title:'写评语',
            content:content,
            id:id,
            save:function () {
                var savedata = Web.formJson('#'+id);
                var dom = $(this);
                if(savedata.comment == ''){
                    Web.ModalAlert(dom,'【评语】不可为空!');
                    return;
                }
                savedata.pid = item;
                console.log(savedata);
                Web.ApiRequest('article/comment_save',savedata,function (data) {
                    if(data.code == -1){
                        Web.ModalAlert(dom,data.msg);
                    }
                    else $('#'+id).modal('hide');
                });

            }
        });
    }
    // 写评语
    $('#write_cmt_lnk').click(function () {
        writeCommentDs();
    });
});
