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
                        <button type='button' data="0" class="layui-btn layui-btn-small"><i class="layui-icon">&#xe628;</i> 系统菜单列表</button>
                        <button class="layui-btn" onclick="location.reload();" style="float:right;"><i class="layui-icon">&#x1002;</i> 刷新 </button>
                    </blockquote>
                    <table class="layui-table layui-form">
                        <thead>
                            <tr>
                                <th colspan="3">菜单名称</th>
                                <th>菜单图标</th>
                                <th>控制器/方法</th>
                                <th>排序</th>
                                <th>状态</th>
                                <th>管理</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(is_array($menu)): foreach($menu as $k=>$vo): ?><tr>
                                <?php $opt = explode('/',$vo['menu_name']); ?>
                                <td><?php if($vo[level] == 0): echo ($vo["title"]); endif; ?></td>
                                <td><?php if($vo[level] == 1): echo ($vo["title"]); endif; ?></td>
                                <td><?php if($vo[level] == 2): echo ($vo["title"]); endif; ?></td>
                                <td><?php echo ($vo['icon']); ?></td>
                                <td><?php echo ($vo['menu_name']); ?></td>
                                <td><input type="text" name="sort" value="<?php echo ($vo['sort']); ?>" onchange="updateSort('AdminMenu', 'id', '<?php echo ($vo["id"]); ?>', 'sort', this)" onkeyup="this.value = this.value.replace(/[^\d]/g, '')" onpaste="this.value=this.value.replace(/[^\d]/g,'')" class="layui-input" style="width: 100px;"></td>
                                <td>
                                    <?php if($vo["id"] != 21): ?><input name="close" lay-skin="switch" autocomplete='off' lay-filter='switchStatus' value='<?php echo ($vo["status"]); ?>' data-value="<?php echo ($vo['id']); ?>" lay-text="开启|关闭" <?php if($vo['status'] == 1): ?>checked<?php endif; ?> type="checkbox"><?php endif; ?>
                                </td>
                                <td><a data="<?php echo ($vo["id"]); ?>" class="layui-btn layui-btn-mini layui-btn-normal editAuthMenuName"><i class="layui-icon">&#xe642;</i>编辑菜单名称&nbsp;&nbsp;(<?php echo ($vo['id']); ?>)</a></td>
                            </tr><?php endforeach; endif; ?>
                        </tbody>
                    </table>
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
            layui.use([ 'layer', 'form'], function() {
                var $ = layui.jquery,form=layui.form;
                form.on('switch(switchStatus)', function(data){
                    var obj = data.elem;
                    var val = data.value;
                    var id = $(obj).data('value');
                    console.log(id);
                    val = (val == 1 ? 2 : 1);
                    var url = "<?php echo U('Menu/saveStatus');?>";
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
            });
            $('.editAuthMenuName').click(function(){
                var id = $(this).attr('data');
                layer.prompt({title:'请输入新的菜单名称'}, function(value, index, elem){
                    $.ajax({
                        type:'post',
                        data:{id:id, name:value},
                        url:"<?php echo U('Menu/editMenuName');?>",
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