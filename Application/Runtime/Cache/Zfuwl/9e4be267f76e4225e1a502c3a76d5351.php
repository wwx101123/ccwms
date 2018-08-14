<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
    <head>
    <meta charset="utf-8">
<title><?php echo ($config['webInfo_web_title']); ?>后台管理</title>
<meta name="renderer" content="webkit">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
<link rel="stylesheet" href="/Public/plugins/layuiadmin/layui/css/layui.css" media="all">
<link rel="stylesheet" href="/Public/plugins/layuiadmin/style/admin.css" media="all">
<link rel="stylesheet" href="/Public/plugins/font-awesome/css/font-awesome.min.css" media="all">
<link rel="stylesheet" href="/Public/css/page.css">
<script type="text/javascript" src="/Public/js/jquery-1.8.3.min.js"></script>
</head>
<body>
    <div class="admin-main">
        <fieldset class="layui-elem-field">
            <blockquote class="layui-elem-quote">
                <button class="layui-btn layui-btn-small"><i class="layui-icon">&#xe628;</i><?php echo ($info[user_name]); ?> 角色分配</button>
                <button  type="button" class="layui-btn layui-btn-danger pull-right" onclick="history.go(-1);" style="float:right;"><i class="layui-icon">&#xe603;</i> 返回</button>
                <button class="layui-btn" onclick="location.reload();" style="float:right;"><i class="layui-icon">&#x1002;</i> 刷新 </button>
            </blockquote>
            <div class="layui-field-box">
                <form class="layui-form">
                    <div class="layui-form-item">
                        <label class="layui-form-label">角色列表</label>
                        <?php if(is_array($list)): foreach($list as $key=>$v): ?><input type="checkbox" name="group_id[<?php echo ($key); ?>]" value="<?php echo ($v["id"]); ?>" <?php if($v['uid']): ?>checked<?php endif; ?> title="<?php echo ($v["title"]); ?>" /><?php endforeach; endif; ?>
                    </div>
                    <input type="hidden" name="id" value="<?php echo ($admin_id); ?>" />
                    <div class="layui-form-item">
                        <div class="layui-input-block"><button class="layui-btn" lay-submit lay-filter="role">立即提交</button></div>
                    </div>
                </form>
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
        layui.use('form', function () {
            var form = layui.form, $ = layui.jquery
            $("button[type=reset]").click();
            form.on('submit(role)', function (data) {
                var roleInfo = data.field;
                var url = "<?php echo U('AuthGroup/giveRole');?>";
                $.post(url, roleInfo, function (data) {
                    if (data.status != 1) {
                        layer.msg(data.msg, {icon: 5});
                        return;
                    } else {
                        layer.msg(data.msg, {icon: 6, time: 2000}, function () {
                            location.reload();
                        });
                    }
                });
                return false;//阻止表单跳转
            });
        });
    </script>
</body>
</html>