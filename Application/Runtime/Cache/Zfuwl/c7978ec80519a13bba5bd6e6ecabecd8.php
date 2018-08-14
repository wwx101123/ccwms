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
            <form action="<?php echo U('Excel/exportUserDay');?>" id="search-form2">
                <div class="layui-inline"><div class="layui-input-inline"><input type="text" name="user_name" placeholder="管理员账号" class="layui-input" /></div></div>
                <div class="layui-inline"><div class="layui-input-inline"><input type="text" name="log_info" placeholder="关键词" class="layui-input" /></div></div>
                <div class="layui-inline"><div class="layui-input-inline"><input type="text" name="time" placeholder="操作时间" id="time" class="layui-input" /></div></div>
                <input type="hidden" name="order_by" value="id">
                <input type="hidden" name="sort" value="desc">
                <button type="button" onclick="ajax_get_table('search-form2',1);" class="layui-btn"><i class="layui-icon">&#xe615;</i> 搜索</button>
                <!--<button type="submit" class="layui-btn"><i class="layui-icon">&#xe62d;</i> 导出excel</button>-->
                <button type="button" onclick="location.reload();" class="layui-btn pull-right"><i class="layui-icon">&#x1002;</i> 刷新</button>
                <div style="clear: both;"></div>
            </form>
        </blockquote>
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
        var $ = layui.jquery,laydate=layui.laydate;
        $(document).ready(function(){
            ajax_get_table('search-form2',1);
        });
        laydate.render({ 
            elem: '#time'
            ,range: true,
            format:'yyyy/MM/dd'
          });
    });
    function ajax_get_table(tab, page){
        layer.closeAll();
        var loadVal = layer.load(3);
        cur_page = page;
        $.ajax({
            type: "POST",
            url: "<?php echo U('Admin/logIndex');?>?p=" + page,
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
    function sort(field){
        $("input[name='order_by']").val(field);
        var v = $("input[name='sort']").val() == 'desc' ? 'asc' : 'desc';
        $("input[name='sort']").val(v);
        ajax_get_table('search-form2', cur_page);
    }
</script>
</body>

</html>