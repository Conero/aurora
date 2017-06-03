/**
 * Created by Administrator on 2017/6/1 0001.
 */
$(function () {
    var TreeNode = Web.getJsVar('node');
    function CreateGroupRoleTree() {
        var data = Web.getJsVar('jstree');
        console.log(data);
        $('#group_role_tree')
            .on('changed.jstree', function (e, data) {
                console.log(data);
                var node = data.node;
                if(node.type == 'default'){
                    var xhtml = '<p>'
                            + ' <a href="'+Web._baseurl+'admin/sgroup/edit/uid/'+node.id+'.html" class="btn btn-info">编辑</a>'
                            + ' <a href="'+Web._baseurl+'admin/sgroup/edit/uid/'+node.id+'.html" class="btn btn-danger">删除</a>'
                        + '</p>'
                        ;
                    $('#grp_role_ds').html(xhtml);
                }
            })
            .jstree({'core' :
            {
                data:data
            },
            "types" : {
                "#" : {
                    "max_children" : 1,
                    "max_depth" : 4,
                    "valid_children" : ["root"]
                },
                "default" : {
                    "icon" : "fa fa-cubes",
                    "valid_children" : ["default","file"]
                },
                "role" : {
                    "icon" : "fa fa-cube",
                    "valid_children" : []
                }
            },
            "plugins" : [
                'types'
            ]
        });
    }
    function CreateGroupRole() {
        var gj = go.GraphObject.make;
        var myDiagram = gj(
            go.Diagram,'group_role_ds',
            {
                initialContentAlignment: go.Spot.Center, // center Diagram contents
                "undoManager.isEnabled": true // enable Ctrl-Z to undo and Ctrl-Y to redo
            });
        /**
         * go 类型： GraphLinksModel 手动连线,Model, 树形图 TreeModel
         */
        //var myModel = gj(go.Model);
        var myModel = gj(go.GraphLinksModel);

        // in the model data, each node is represented by a JavaScript object:
        console.log(TreeNode);

        myModel.nodeDataArray = TreeNode;
        myModel.linkDataArray = [];
        (function () {
            for(var k=0; k<TreeNode.length; k++){
                var nd = TreeNode[k];
                if(nd.group){
                    myModel.linkDataArray.push({from:nd.group,to:nd.key});
                }
            }
            console.log(myModel.linkDataArray);
        }());

        myDiagram.model = myModel;
    }
    // jstree 构图
    CreateGroupRoleTree();
    // goJs 构图
    CreateGroupRole();
});