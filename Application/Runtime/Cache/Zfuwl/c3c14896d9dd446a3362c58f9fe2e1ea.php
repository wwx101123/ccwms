<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
    <head><meta charset="utf-8">
<title><?php echo ($config['webInfo_web_title']); ?>后台管理</title>
<meta name="renderer" content="webkit">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
<link rel="stylesheet" href="/Public/plugins/layuiadmin/layui/css/layui.css" media="all">
<link rel="stylesheet" href="/Public/plugins/layuiadmin/style/admin.css" media="all">
<link rel="stylesheet" href="/Public/plugins/font-awesome/css/font-awesome.min.css" media="all">
<link rel="stylesheet" href="/Public/css/page.css">
<script type="text/javascript" src="/Public/js/jquery-1.8.3.min.js"></script></head>
    <body>
        <div class="admin-main">
            <fieldset class="layui-elem-field">
                <div class="layui-field-box" style='overflow:scorll;'>
                    <blockquote class="layui-elem-quote">
                        <form id="search-form2">
                            <button type='button' data="0" class="layui-btn layui-btn-small"><i class="layui-icon">&#xe628;</i> 系统角色列表</button>
                                <div class="layui-inline"><div class="layui-input-inline"><input type="text" name="title" placeholder="角色名称" class="layui-input" /></div></div>
                                <div class="layui-inline"><div class="layui-input-inline"><input type="text" name="note" placeholder="职责简要" class="layui-input" /></div></div>
                                <button type="button" class="layui-btn layui-btn-mini layui-btn-normal add"><i class="layui-icon">&#xe61f;</i> 添加新角色</button>
                                <button class="layui-btn" onclick="location.reload();" style="float:right;"><i class="layui-icon">&#x1002;</i> 刷新 </button>
                            <div style="clear:both;"></div>
                        </form>
                    </blockquote>
                    <table class="layui-table layui-form">
                        <thead>
                            <tr>
                                <th>角色名称</th>
                                <th>职责描述</th>
                                <th>状态</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if(is_array($list)): foreach($list as $key=>$v): ?><tr>
                                <td><?php echo ($v["title"]); ?></td>
                                <td><?php echo ($v["note"]); ?></td>
                                <td>
                                    <?php if($v["id"] != 1): ?><input name="close" lay-skin="switch" autocomplete='off' lay-filter='switchStatus' value='<?php echo ($v["status"]); ?>' data-value="<?php echo ($v['id']); ?>" lay-text="开启|关闭" <?php if($v['status'] == 1): ?>checked<?php endif; ?> type="checkbox"><?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?php echo U('AuthGroup/ruleGroup',array('role_id' => $v['id']));?>" class="layui-btn layui-btn-mini rule"><i class="layui-icon">&#xe614;</i>分配权限</a>
                                    <a data="<?php echo ($v["id"]); ?>" class="layui-btn layui-btn-mini layui-btn-normal editAuthGroupName"><i class="layui-icon">&#xe642;</i>编辑角色名称&nbsp;&nbsp;(<?php echo ($v['id']); ?>)</a>
                                    <?php if($v["id"] != 1): ?><a data="<?php echo ($v["id"]); ?>" class="layui-btn layui-btn-danger layui-btn-mini del"><i class="layui-icon">&#xe640;</i>删除</a><?php endif; ?>
                                </td>
                            </tr><?php endforeach; endif; ?>
                        </tbody>
                    </table>
                <?php echo ($page); ?>
            </div>
        </fieldset>
    </div>
    <script src="/Public/plugins/layuiadmin/layui/layui.js"></script>
<script>
    layui.config({
    base: '/Public/plugins/layuiadmin/' //静态资源所在路径
}).extend({
    index: 'lib/index' //主入口模块
}).use('index');
</script>
<script type="text/javascript" src="/Public/js/zfuwlAjax.js"></script>

    <script>
        layui.use(['laypage', 'layer', 'form'], function () {
            var laypage = layui.laypage, $ = layui.jquery,form=layui.form;
            form.on('switch(switchStatus)', function(data){
                var obj = data.elem;
                var val = data.value;
                var id = $(obj).data('value');
                console.log(id);
                val = (val == 1 ? 2 : 1);
                var url = "<?php echo U('AuthGroup/saveStatus');?>";
                $.post(url, {val:val,id:id}, function(res) {
                    if (res.status == 0) {
                        layer.msg(res.msg, {icon: 5});
                        return;
                    } else {
                        layer.msg(res.msg, {icon: 6, time: 2000, shade:0.01}, function() {
                            location.reload();
                        });
                    }
                });
            });
            //请求表单
            $('.add').click(function () {
                var url = "<?php echo U('AuthGroup/addGroup');?>";
                var getMoreLoad = layer.msg('添加中...', {shade: 0.1, icon: 16, time: 0});
                $.post(url, $('#search-form2').serialize(), function (data) {
                    layer.close(getMoreLoad);
                    if (data.status == 0) {
                        layer.msg(data.msg, {icon: 5});
                        return;
                    } else {
                        layer.msg(data.msg, {icon: 6,shade: 0.1}, function(){
                            location.reload();
                        });
                    }
                    return false;
                })
            });

            //编辑
            $('.edit').click(function () {
                var id = $(this).attr('data');
                var url = "<?php echo U('AuthGroup/editGroup');?>";

                $.get(url, {id: id}, function (data) {
                    if (data.status == 0) {
                        layer.msg(data.msg, {icon: 5});
                        return;
                    }
                    layer.open({
                        title: '编辑角色',
                        type: 1,
                        skin: 'layui-layer-rim', //加上边框
                        area: ['80%'], //宽高
                        content: data
                    });
                })
            });

            //删除
            $('.del').click(function () {
                var id = $(this).attr('data');
                var url = "<?php echo U('AuthGroup/delGroup');?>";
                layer.confirm('确定删除当前角色吗?', {
                    icon: 3,
                    skin: 'layer-ext-moon',
                    btn: ['确认', '取消']
                }, function () {
                    $.post(url, {
                        id: id
                    }, function (data) {
                        if (data.status != 1) {
                            layer.msg(data.msg, {icon: 5});
                            return;
                        } else {
                            layer.msg(data.msg, {icon: 6, time: 2000}, function () {
                                window.location.reload();
                            });
                        }
                    })
                });
            })
        });
        
        $('.editAuthGroupName').click(function(){
            var id = $(this).attr('data');
            layer.prompt({title:'请输入新的角色名称'}, function(value, index, elem){
                $.ajax({
                    type:'post',
                    data:{id:id, name:value},
                    url:"<?php echo U('AuthGroup/editAuthName');?>",
                    success:function(data){
                        layer.close(index);
                        if (data.status == 0) {
                            layer.msg(data.msg, {icon: 5});
                        } else if (data.status == 1) {
                            layer.msg(data.msg, {icon: 6, time: 2000}, function () {
                                location.reload();
                            });
                        }
                    }
                })
            });
        });
    </script>
</body>

</html>