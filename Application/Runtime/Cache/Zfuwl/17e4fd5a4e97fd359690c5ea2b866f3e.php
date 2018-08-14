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
            <form  class='layui-form' id="search-form2">
                <div class="layui-inline">
                    <input type="text" name="parent_id" value="<?php echo ($_GET['parent_id']); ?>" readonly placeholder="地址编号" class="layui-input" />
                </div>
                <div class="layui-inline">
                    <input type="text" name="name_cn" value="<?php echo ($info[name_cn]); ?>" placeholder="国家名称" class="layui-input" />
                </div>
                <input type="hidden" name="order_by" value="id">
                <input type="hidden" name="sort" value="desc">
                <button class="layui-btn" type="button" onclick="ajax_get_table('search-form2', 1);">搜索</button>
                <?php if($_GET['parent_id'] > 0): ?><button  type="button" class="layui-btn layui-btn-danger" onclick="history.go(-1);"><i class="layui-icon">&#xe603;</i> 返回</button>
                    <a href="<?php echo U('Website/regionAdd',array('parent_id'=>$info['id']));?>">
                        <button type="button" class="layui-btn layui-btn-normal">新增地址</button>
                    </a>
                <?php else: ?>
                    <a href="<?php echo U('Website/regionAdd');?>">
                        <button type="button" class="layui-btn layui-btn-normal">新增省份</button>
                    </a><?php endif; ?>
                <button type="button" class="layui-btn layui-btn-normal createDataCity">生成前台地址</button>
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
    layui.use(['laydate', 'layer', 'form'], function () {
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
            url: "<?php echo U('Website/regionIndex');?>?p=" + page,
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
    $(document).on('click', '.createDataCity', function(){
        layer.confirm('是否确认重新生成?', {icon:3}, function(){
            var loadAdd = layer.msg('生成中', {icon:16,shade:0.1,time:0});
            $.ajax({
                url:"<?php echo U('Api/createDataCity');?>",
                type:'post',
                success:function(data) {
                    layer.close(loadAdd);
                    if(data.status == 1) {
                        layer.msg(data.msg, {icon:6});
                    } else {
                        layer.msg(data.msg, {icon:5});
                    }
                }
            });
        });
    });
    function sort(field){
        $("input[name='order_by']").val(field);
        var v = $("input[name='sort']").val() == 'desc' ? 'asc' : 'desc';
        $("input[name='sort']").val(v);
        ajax_get_table('search-form2', cur_page);
    }
</script>
</body>

</html>