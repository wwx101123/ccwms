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
            <div class="test-table-reload-btn" style="margin-top: 10px;margin-left:15px;">
                <form action="<?php echo U('Excel/exportUserList');?>" class='layui-form' id="search-form2">
                    <div class="layui-inline" style='width:100px;'>
                        <select name="type">
                            <option value="2">账号</option>
                            <option value="3">手机</option>
                            <option value="4">邮箱</option>
                            <option value="5">姓名</option>
                            <option value="6">呢称</option>
                            <option value="7">身份证</option>
                          	<option value="1">ID</option>
                        </select>
                    </div>
                    <div class="layui-inline">
                        <input type="text" name="account" placeholder="ID/帐号/手机/邮箱/姓名/呢称/身份证" class="layui-input">
                    </div>
                    <div class="layui-inline">
                        <select name="level" class="layui-input">
                            <option value="">--会员等级--</option>
                            <?php if(is_array($levelInfo)): foreach($levelInfo as $k=>$v): ?><option value="<?php echo ($k); ?>"><?php echo ($v); ?></option><?php endforeach; endif; ?>
                        </select>
                    </div>
                    <input type="hidden" name="order_by" value="user_id">
                    <input type="hidden" name="sort" value="desc">
                    <input type="hidden" name="timeName" value="reg_time">
                    <button class="layui-btn" type="button" onclick="ajax_get_table('search-form2', 1);">搜索</button>
                    <button type="submit" class="layui-btn">导出</button>
                    <button type="button" class="layui-btn send" data-url="<?php echo U('User/sendEmail');?>" data-title="邮件通知">邮件通知</button>
                    <button type="button" class="layui-btn send" data-url="<?php echo U('User/sendMessage');?>" data-title="站内通知">站内通知</button>
                    <button type="button" class="layui-btn send" data-url="<?php echo U('User/sendSms');?>" data-title="短信通知">短信通知</button>
                    <a href="<?php echo U('User/addUser');?>">
                        <button type="button" class="layui-btn layui-btn-normal pull-right" >添加会员</button>
                    </a>
                </form>
            </div>
            <div class="layui-field-box"><div id="ajax_return"></div></div>
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
    layui.use(['laydate', 'layer', 'form'], function () {
        $ = layui.jquery;

        $(document).ready(function () {
            ajax_get_table('search-form2', 1);
        });
        $('.send').click(function(){
            var id = 0;
            var obj = $("input[name*='selected']");
            if (obj.is(":checked")) {
                var check_val = [];
                for (var k in obj) {
                    if (obj[k].checked)
                        check_val.push(obj[k].value);
                }
                id = check_val.join(',');
            }
            if(!id) {
                layer.msg('请选择会员');return false;
            }
            var title = $(this).data('title');
            $.get($(this).data('url'), {user_id: id}, function (data) {
                if (data.status == 0) {
                    layer.msg(data.msg, {icon: 5});
                    return;
                }
                layer.open({
                    title: title,
                    type: 1,
                    skin: 'layui-layer-rim', //加上边框
                    area: ['80%', '80%'], //宽高
                    content: data
                });
            });
        });
    });

    function saveData(fieldVal, val, user_id){
        $.ajax({
            type: "POST",
            url: "<?php echo U('User/saveState');?>",
            data: {fieldVal: fieldVal, user_id: user_id, val: val},
            success: function (data) {
                if (data.status == 0) {
                    layer.msg(data.msg, {icon: 5});
                    return;
                }
                var page = $('.pagination .active').find('a').data('p');
                ajax_get_table('search-form2', page);
            }
        });
    }

    function ajax_get_table(tab, page){
        var loadVal = layer.load(3);
        cur_page = page;
        $.ajax({
            type: "POST",
            url: "<?php echo U('User/index');?>?p=" + page,
            data: $('#' + tab).serialize(),
            success: function (data) {
                if (data.status == 0) {
                    layer.msg(data.msg, {icon: 5});
                    return;
                }
                layer.close(loadVal);
                $("#ajax_return").html(data);
            }
        });
    }
    // 点击排序
    function sort(field){
        $("input[name='order_by']").val(field);
        var v = $("input[name='sort']").val() == 'desc' ? 'asc' : 'desc';
        $("input[name='sort']").val(v);
        ajax_get_table('search-form2', cur_page);
    }
</script>
</body>

</html>