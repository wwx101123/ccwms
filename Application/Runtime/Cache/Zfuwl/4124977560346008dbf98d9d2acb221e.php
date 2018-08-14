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
                <div class="test-table-reload-btn" style="margin-top: 10px;margin-left:15px;">
                    <a lay-href="<?php echo U('Admin/addUser');?>" class="layui-btn layui-btn-sm layui-btn-normal">添加新管理员</a>
                </div>
                <table class="layui-table layui-form">
                    <thead>
                        <tr>
                            <th>用户名</th>
                            <th>手机号</th>
                            <th>邮箱</th>
                            <th>最近登录时间</th>
                            <th>状态</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if(is_array($userList)): foreach($userList as $k=>$vo): ?><tr>
                        <td><?php echo ($vo["user_name"]); ?></td>
                        <td><?php echo ($vo["mobile"]); ?></td>
                        <td><?php echo ($vo["email"]); ?></td>
                        <td>
                            <?php if($vo["last_login"] <= 0): ?>暂未登录
                            <?php else: ?>
                                <?php echo (date("Y-m-d H:i:s",$vo["last_login"])); endif; ?>
                        </td>
                        <td>
                            <?php if($vo["admin_id"] > 1): ?><input name="close" lay-skin="switch" autocomplete='off' lay-filter='switchStatus' value='<?php echo ($vo["status"]); ?>' data-value="<?php echo ($vo['admin_id']); ?>" lay-text="开启|关闭" <?php if($vo['status'] == 1): ?>checked<?php endif; ?> type="checkbox"><?php endif; ?>
                        </td>
                        <td>
                            <a lay-href="<?php echo U('Admin/editUser',array('id'=>$vo['admin_id']));?>" class="layui-btn layui-btn-sm layui-btn-normal">编辑</a>
                            <a href="<?php echo U('AuthGroup/giveRole',array('id'=>$vo['admin_id']));?>" class="layui-btn layui-btn-sm"><i class="layui-icon">&#xe614;</i>分配角色</a>
                            <?php if($vo["admin_id"] > 1): ?><a data="<?php echo ($vo["admin_id"]); ?>" class="layui-btn layui-btn-danger layui-btn-sm adminUserDel"><i class="layui-icon">&#xe640;</i>删除</a><?php endif; ?>
                        </td>
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
    layui.use(['layer', 'form'], function () {
        var $ = layui.jquery, form = layui.form;
        form.on('switch(switchStatus)', function (data) {
            var obj = data.elem;
            var val = data.value;
            var id = $(obj).data('value');
            console.log(id);
            val = (val == 1 ? 2 : 1);
            var url = "<?php echo U('Admin/saveStatus');?>";
            $.post(url, {val: val, id: id}, function (res) {
                if (res.status == 0) {
                    layer.msg(res.msg, {icon: 5});
                    return;
                } else {
                    layer.msg(res.msg, {icon: 6, time: 2000, shade: 0.01}, function () {
                        location.reload();
                    });
                }
            });
        });
    });
    $('.adminUserDel').click(function () {
        var url = "<?php echo U('Admin/delUser');?>";
        var id = $(this).attr('data');
        if (!id) {
            var obj = $("input[name*='selected']");
            if (obj.is(":checked")) {
                var check_val = [];
                for (var k in obj) {
                    if (obj[k].checked)
                        check_val.push(obj[k].value);
                }
                id = check_val;
            }
        }
        if (!id) {
            return false;
        }
        layer.confirm('确定删除吗?', {icon: 3, skin: 'layer-ext-moon', btn: ['确认', '取消']}, function () {
            $.post(url, {id: id}, function (data) {
                if (data.status == 0) {
                    layer.msg(data.msg, {icon: 5});
                } else if (data.status == 1) {
                    layer.msg(data.msg, {icon: 6, time: 2000}, function () {
                        location.reload();
                    });
                }
            });
        });
    });
</script>
</body>
</html>