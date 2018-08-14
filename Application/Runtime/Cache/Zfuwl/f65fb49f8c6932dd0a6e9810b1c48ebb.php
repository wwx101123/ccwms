<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Lazy Load Tree Nodes - jQuery EasyUI Demo</title>
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
    <link rel="stylesheet" type="text/css" href="/Public/plugins/easyui/themes/default/easyui.css">
    <script type="text/javascript" src="/Public/js/jquery.min.js"></script>
    <script type="text/javascript" src="/Public/plugins/easyui/jquery.easyui.min.js"></script>
</head>
<body>
    <div class="admin-main">
        <fieldset class="layui-elem-field">
            <div class="test-table-reload-btn" style="margin-top: 10px;margin-left:15px;">
                <form id="search-form2">
                    <div class="layui-inline"><div class="layui-input-inline"><input type="text" name="account" id="account" placeholder="会员帐号" class="layui-input"></div></div>
                    <button type="button" onclick="showCategory();" class="layui-btn"><i class="layui-icon">&#xe615;</i> 搜索</button>
                    <div style="clear: both;"></div>
                </form>
            </div>
            <div class="layui-field-box"><div id="MyTree"></div></div>
        </fieldset>
    </div>
    <script>
        function showCategory() {
            account = $('#account').val();
            $('#MyTree').tree({
                checkbox: false,
                url: '<?php echo U("Index/getUser");?>?account=' + account,
                animate: true,
                lines: true,
                onLoadSuccess: function () {
                    $('#MyTree').tree('options').url = '<?php echo U("Index/getUser");?>'; // 去除账号重新设置url
                },
                onClick: function (node) {
                    var state = node.state;
                    if (!state) {                                   //判断当前选中的节点是否为根节点
                        currentId = node.id;
                        $("#chooseOk").attr("disabled", false);   //如果为根节点 则OK按钮可用
                    } else {
                        $("#chooseOk").attr("disabled", true);    //如果不为根节点 则OK按钮不可用
                    }
                }
            });
        }
        showCategory();
    </script>
</body>
</html>