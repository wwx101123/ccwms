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
                <button class="layui-btn layui-btn-small"><i class="layui-icon">&#xe628;</i> 系统权限分配</button>
                <button  type="button" class="layui-btn layui-btn-danger pull-right" onclick="history.go(-1);" style="float:right;"><i class="layui-icon">&#xe603;</i> 返回</button>
                <button class="layui-btn" onclick="location.reload();" style="float:right;"><i class="layui-icon">&#x1002;</i> 刷新 </button>
            </blockquote>
            <div class="layui-field-box">
                <form class="layui-form">
                     <div class="layui-form-item layui-form-text">
                        <label class="layui-form-label">职责描述</label>
                        <div class="layui-input-block">
                            <textarea placeholder="请输入职责描述" class="layui-textarea" name="note"><?php echo ($note); ?></textarea>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">权限列表</label>
                        <?php if(is_array($menus)): foreach($menus as $k=>$vo): ?><div class="layui-input-block">
                                <input type="checkbox" name="menu[<?php echo ($vo["id"]); ?>]" value="<?php echo ($vo['id']); ?>" <?php if(in_array($vo['id'], $rulesArr)): ?>checked<?php endif; ?> title="<?php echo ($vo["title"]); ?>" class="level_one" />
                                <?php if(is_array($vo[$vo['id']])): foreach($vo[$vo['id']] as $key=>$v): ?><div class="layui-input-block">
                                        <input type="checkbox" name="menu[<?php echo ($v["id"]); ?>]" value="<?php echo ($v['id']); ?>" <?php if(in_array($v['id'], $rulesArr)): ?>checked<?php endif; ?> title="<?php echo ($v["title"]); ?>" class="level_two" />
                                        <div class="layui-input-block">
                                            <?php if(is_array($v[$v['id']])): foreach($v[$v['id']] as $key=>$v1): ?><input type="checkbox" name="menu[<?php echo ($v1["id"]); ?>]" value="<?php echo ($v1['id']); ?>" <?php if(in_array($v1['id'], $rulesArr)): ?>checked<?php endif; ?> title="<?php echo ($v1["title"]); ?>" class="level_three" /><?php endforeach; endif; ?>
                                        </div>
                                    </div><?php endforeach; endif; ?>
                            </div><?php endforeach; endif; ?>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <button class="layui-btn" lay-submit lay-filter="auth">立即提交</button>
                            <button class="layui-btn layui-btn-primary" onclick="window.history.back(-1)">返回</button>
                        </div>
                    </div>
                    <input type="hidden" name="role_id" value="<?php echo ($role_id); ?>">
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
        layui.use(['layer', 'form'], function () {
            var form = layui.form,$ = layui.jquery;
            //选中
            $('.layui-form-checkbox').on('click', function (e) {
                var children = $(this).parent('.layui-input-block').find('.layui-form-checkbox');
                var input = $(this).parent('.layui-input-block').find('input');

                if ($(this).prev('input').hasClass('level_three')) {
                    if ($(this).hasClass('layui-form-checked') == true) {
                        $(this).addClass('layui-form-checked');
                        $(this).prev('input').prop('checked', true);
                    } else {
                        $(this).removeClass('layui-form-checked');
                        $(this).prev('input').prop('checked', false);
                    }
                } else {
                    if ($(this).hasClass('layui-form-checked') == true) {
                        children.addClass('layui-form-checked');
                        input.prop('checked', true);
                    } else {
                        children.removeClass('layui-form-checked');
                        input.prop('checked', false);
                    }

                }

            });
            //监听提交
            form.on('submit(auth)', function (data) {
                var menu_ids = data.field;
                var url = "<?php echo U('AuthGroup/ruleGroup');?>";
                $.post(url, menu_ids, function (data) {
                    if (data.status != 1) {
                        layer.msg(data.msg, {icon: 5});
                        return;
                    } else {
                        layer.msg(data.msg, {icon: 6, time: 2000}, function () {
                            history.go(-1);
                        });
                    }
                });
                return false;//阻止表单跳转
            });
        });
    </script>
</body>
</html>