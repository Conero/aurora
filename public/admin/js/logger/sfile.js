/**
 * Created by Administrator on 2017/5/19 0019.
 */
$(function () {
    var QueryDir = Web.getQuery('dir');
    // 父类跳转
    (function () {
        if(QueryDir){
            var tmpArry = QueryDir.split('/');
            var dir = '';
            var aElArray = [];
            for(var i=0; i<tmpArry.length; i++){
                dir += tmpArry[i];
                aElArray.push('<a href="'+Web.updateQuery({dir:dir})+'">'+tmpArry[i]+'</a>');
                dir += '/';
            }
            // 删除目录以及目录展示
            $('#parent_path').html(aElArray.join('/')
                +' <a href="'+Web.updateQuery({type:'2d'})+'"><i class="fa fa-trash"></i></a>'
            );
        }
    }());

    // 树形图展示
    $('#sfile_tree')
    // listen for event
        .on('changed.jstree', function (e, data) {
            //console.log(e);
            console.log(data);
            var node = data.node;
            var baseName = data.instance.get_node(node.parent).text;
            // 文件
            if(node.type == 'file'){
                var file = baseName + '/' + node.text;
                var xhtml = '<div class="card-block">'
                    + '<h6 class="card-title">'+node.text
                    + ' <a href="'+Web.updateQuery({file:file,type:'fd'})+'"><i class="fa fa-trash"></i></a>'
                    +'</h6>'
                    + '</div>'
                    ;
                var el = $(xhtml);
                $.post(Web._baseurl+'admin/logger/sfile_get',{name:file,type:node.type,item:'get_content'},function (data) {
                    //var contentXhtml = '<div class="card-text">'+data+'</div>';
                    var contentXhtml = '<textarea class="form-control" rows="50">'+data+'</textarea>';
                    el.append(contentXhtml);
                    $('#sfile_tree_div').html(el);
                });
            }else{
                if(baseName){
                    var dir = baseName + '/' + node.text;
                    location.href = Web.updateQuery({dir:dir});
                }
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
