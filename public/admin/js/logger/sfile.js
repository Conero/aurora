/**
 * Created by Administrator on 2017/5/19 0019.
 */
$(function () {
    // 树形图展示
    $('#sfile_tree')
    // listen for event
        .on('changed.jstree', function (e, data) {
            //console.log(e);
            console.log(data);
            var node = data.node;
            // 文件
            if(node.type == 'file'){
                var xhtml = '<div class="card-block">'
                    + '<h6 class="card-title">'+node.text+'</h6>'
                    + '</div>'
                    ;
                var file = node.parent + '/' + node.text;
                var el = $(xhtml);
                $.post(Web._baseurl+'admin/logger/sfile_get',{name:file,type:node.type,item:'get_content'},function (data) {
                    //var contentXhtml = '<div class="card-text">'+data+'</div>';
                    var contentXhtml = '<textarea class="form-control" rows="50">'+data+'</textarea>';
                    el.append(contentXhtml);
                    $('#sfile_tree_div').html(el);
                });
            }else{
                return;
                var dir = node.parent + '/' + node.text;
                location.href = Web.updateQuery({dir:dir});
            }
        })
        .jstree({
        'core' : {
            'data' : {
                'url' : function (node) {
                    return 'sfile_get';
                },
                'data' : function (node) {
                    return { 'dir' : Web.getQuery('dir')};
                }
            }
        },
            "types" : {
                "#" : {
                    "max_children" : 1,
                    "max_depth" : 4,
                    "valid_children" : ["root"]
                },
                "default" : {
                    "valid_children" : ["default","file"]
                },
                "file" : {
                    "icon" : "fa fa-file",
                    "valid_children" : []
                }
            },
            "plugins" : [
                'types'
            ]
    });
});