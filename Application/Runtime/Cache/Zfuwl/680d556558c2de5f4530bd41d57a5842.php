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
            <form id="search-form2">
                <div class="layui-inline">
                    <input type="text" name="title" placeholder="分类名称" class="layui-input" />
                </div>
                <button type="button" onclick="ajax_get_table('search-form2',1);" class="layui-btn">搜索</button>
                <!--<a href="<?php echo U('Article/addCat');?>" class="layui-btn layui-btn-normal"> 新增</a>-->
                <div style="clear: both;"></div>
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
    layui.use(['laypage', 'layer', 'form'], function () {
        var $ = layui.jquery;
        $(document).ready(function(){
            ajax_get_table('search-form2',1);
        });
    });
    function ajax_get_table(tab, page){
        var loadVal = layer.load(3);
        cur_page = page;
        $.ajax({
            type: "POST",
            url: "<?php echo U('Article/catIndex');?>?p=" + page,
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
</script>
</body>

</html>