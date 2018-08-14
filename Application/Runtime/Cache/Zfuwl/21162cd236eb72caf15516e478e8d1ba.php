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

    <script>
        /^http(s*):\/\//.test(location.href) || alert('请先部署到 localhost 下再访问');
    </script>
</head>
<body class="layui-layout-body">
  <div id="LAY_app">
    <div class="layui-layout layui-layout-admin">
        <div class="layui-header">
            <!-- 头部区域 -->
            <ul class="layui-nav layui-layout-left">
              <li class="layui-nav-item layadmin-flexible" lay-unselect>
                <a href="javascript:;" layadmin-event="flexible" title="侧边伸缩">
                    <i class="layui-icon layui-icon-shrink-right" id="LAY_app_flexible"></i>
                </a>
            </li>
         <!--   <li class="layui-nav-item layui-hide-xs releaseShares" lay-unselect>
                <a href='javascript:void(0);' target="_blank" title="测试分红">测试分红</a>
            </li>
           <li class="layui-nav-item layui-hide-xs clearCj" lay-unselect>
                <a href='javascript:void(0);' target="_blank" title="测试出局">测试出局</a>
            </li>-->
            <!--<li class="layui-nav-item layui-hide-xs judgeFrozen" lay-unselect>
                <a href='javascript:void(0);' target="_blank" title="测试冻结">测试冻结</a>
            </li>
            <li class="layui-nav-item layui-hide-xs releaseShares" lay-unselect>
                <a href='javascript:void(0);' target="_blank" title="测试释放">测试释放</a>
            </li>-->
            <li class="layui-nav-item layui-hide-xs" lay-unselect>
                <a href="<?php echo U('/');?>" target="_blank" title="前台">
                    <i class="layui-icon layui-icon-website"></i>
                </a>
            </li>
            <li class="layui-nav-item layui-hide-xs clearCache" lay-unselect>
                <a href='javascript:void(0);' title="清除缓存">
                    <i class="fa fa-connectdevelop"></i>
                </a>
            </li>
        <!--    <li class="layui-nav-item layui-hide-xs clearTable" lay-unselect>
                <a href='javascript:void(0);' title="清空数据库">
                    <i class="fa fa-bolt"></i>
                </a>
            </li>-->
            <li class="layui-nav-item" lay-unselect>
                <a href="javascript:;" layadmin-event="refresh" title="刷新">
                    <i class="layui-icon layui-icon-refresh-3"></i>
                </a>
            </li>
        </ul>
        <ul class="layui-nav layui-layout-right" lay-filter="layadmin-layout-right">

       <!--  <li class="layui-nav-item" lay-unselect>
           <a lay-href="app/message/index.html" layadmin-event="message" lay-text="消息中心">
               <i class="layui-icon layui-icon-notice"></i>
               如果有新消息，则显示小圆点
               <span class="layui-badge-dot"></span>
           </a>
       </li> -->
       <li class="layui-nav-item layui-hide-xs" lay-unselect>
        <a href="javascript:;" layadmin-event="theme">
            <i class="layui-icon layui-icon-theme"></i>
        </a>
    </li>
    <li class="layui-nav-item layui-hide-xs" lay-unselect>
        <a href="javascript:;" layadmin-event="note">
            <i class="layui-icon layui-icon-note"></i>
        </a>
    </li>
    <li class="layui-nav-item" lay-unselect>
        <a href="javascript:;">
            <cite><?php echo ($userInfo['user_name']); ?></cite>
        </a>
        <dl class="layui-nav-child">
            <dd><a lay-href="<?php echo U('Admin/editUser', array('id' => session('admin_id')));?>">基本资料</a></dd>
            <hr>
            <dd class="logout" style="text-align: center;"><a>退出</a></dd>
        </dl>
    </li>

        <!-- <li class="layui-nav-item layui-hide-xs" lay-unselect>
            <a href="javascript:;" layadmin-event="about"><i class="layui-icon layui-icon-more-vertical"></i></a>
        </li>
        <li class="layui-nav-item layui-show-xs-inline-block layui-hide-sm" lay-unselect>
            <a href="javascript:;" layadmin-event="more"><i class="layui-icon layui-icon-more-vertical"></i></a>
        </li> -->
    </ul>
</div>

<!-- 侧边菜单 -->
<div class="layui-side layui-side-menu">
    <div class="layui-side-scroll">
        <div class="layui-logo" lay-href="<?php echo U('Index/welcome');?>">
            <span><?php echo ($config['webInfo_web_name']); ?></span>
        </div>
        <ul class="layui-nav layui-nav-tree" lay-shrink="all" id="LAY-system-side-menu" lay-filter="layadmin-system-side-menu">
            <li data-name="home" class="layui-nav-item">
                <a href="javascript:;" lay-tips="主页" lay-direction="2">
                    <i class="layui-icon layui-icon-home"></i>
                    <cite>主页</cite>
                </a>
                <dl class="layui-nav-child">
                    <dd data-name="console" class="layui-this">
                        <a lay-href="<?php echo U('Index/welcome');?>">控制台</a>
                    </dd>
                </dl>
            </li>
            <?php if(is_array($menus)): foreach($menus as $key=>$v): ?><li data-name="<?php echo ($v["title"]); ?>" class="layui-nav-item">
                    <a href="javascript:;" lay-tips="<?php echo ($v['title']); ?>" lay-direction="2">
                        <i class="layui-icon layui-icon-component"></i>
                        <cite><?php echo ($v["title"]); ?></cite>
                    </a>
                    <dl class="layui-nav-child">
                        <?php if(is_array($v[$v['id']])): foreach($v[$v['id']] as $key=>$val): ?><dd data-name="<?php echo ($val["title"]); ?>">
                                <a lay-href="<?php echo U($val['menu_name']);?>"><?php echo ($val["title"]); ?></a>
                            </dd><?php endforeach; endif; ?>
                    </dl>
                </li><?php endforeach; endif; ?>
        </ul>
    </div>
</div>

<!-- 页面标签 -->
<div class="layadmin-pagetabs" id="LAY_app_tabs">
    <div class="layui-icon layadmin-tabs-control layui-icon-prev" layadmin-event="leftPage"></div>
    <div class="layui-icon layadmin-tabs-control layui-icon-next" layadmin-event="rightPage"></div>
    <div class="layui-icon layadmin-tabs-control layui-icon-down">
        <ul class="layui-nav layadmin-tabs-select" lay-filter="layadmin-pagetabs-nav">
            <li class="layui-nav-item" lay-unselect>
                <a href="javascript:;"></a>
                <dl class="layui-nav-child layui-anim-fadein">
                    <dd layadmin-event="closeThisTabs"><a href="javascript:;">关闭当前标签页</a></dd>
                    <dd layadmin-event="closeOtherTabs"><a href="javascript:;">关闭其它标签页</a></dd>
                    <dd layadmin-event="closeAllTabs"><a href="javascript:;">关闭全部标签页</a></dd>
                </dl>
            </li>
        </ul>
    </div>
    <div class="layui-tab" lay-unauto lay-allowClose="true" lay-filter="layadmin-layout-tabs">
      <ul class="layui-tab-title" id="LAY_app_tabsheader">
        <li lay-id="<?php echo U('Index/welcome');?>" lay-attr="<?php echo U('Index/welcome');?>" class="layui-this"><i class="layui-icon layui-icon-home"></i></li>
    </ul>
</div>
</div>


<!-- 主体内容 -->
<div class="layui-body" id="LAY_app_body">
    <div class="layadmin-tabsbody-item layui-show">
      <iframe src="<?php echo U('Index/welcome');?>" frameborder="0" class="layadmin-iframe"></iframe>
  </div>
</div>

<!-- 辅助元素，一般用于移动设备下遮罩 -->
<div class="layadmin-body-shade" layadmin-event="shade"></div>
</div>
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

<script type="text/javascript">

    $('.logout').click(function() {
        layer.confirm('是否确认退出?', {
            icon: 3,
            title: '温馨提示'
        }, function() {
            var url = "<?php echo U('Index/logout');?>";
            $.post(url, function(data) {
                layer.msg('退出成功!', {
                    icon: 6
                }, function() {
                    window.location.href = '<?php echo U("Login/login");?>';
                });
            });
        });
    });
    $('.clearCache').click(function() {
        var url = "<?php echo U('Index/cleanCache');?>";
        $.post(url, function(data) {
            if (data.status != 1) {
                layer.msg(data.msg, {
                    icon: 5
                });
            } else {
                layer.msg(data.msg, {
                    icon: 6
                });
            }
        });
    });
    $('.clearCj').click(function() {
        var url = "<?php echo U('Api/judgeCj');?>";
        $.post(url, function(data) {
            if (data.status != 1) {
                layer.msg(data.msg, {
                    icon: 5
                });
            } else {
                layer.msg(data.msg, {
                    icon: 6
                });
            }
        });
    });
    $('.judgeFrozen').click(function() {
        var url = "<?php echo U('Api/judgeFrozen');?>";
        $.post(url, function(data) {
            if (data.status != 1) {
                layer.msg(data.msg, {
                    icon: 5
                });
            } else {
                layer.msg(data.msg, {
                    icon: 6
                });
            }
        });
    });
    $('.releaseShares').click(function() {
        var url = "<?php echo U('Api/daySj');?>";
        $.post(url, function(data) {
            if (data.status != 1) {
                layer.msg(data.msg, {
                    icon: 5
                });
            } else {
                layer.msg(data.msg, {
                    icon: 6
                });
            }
        });
    });
    $('.clearTable').click(function() {
        layer.confirm('是否确认清空数据，该操作不可撤销?', {
            icon: 3,
            title: '温馨提示'
        }, function() {
            var getMoreLoad = layer.msg('数据清空中...', {
                shade: 0.3,
                icon: 16,
                time: 0
            });
            var url = "<?php echo U('Tools/oneKeyClearTable');?>";
            $.post(url, function(data) {
                layer.close(getMoreLoad);
                if (data.status == 0) {
                    layer.msg(data.msg, {
                        icon: 5
                    });
                    return;
                } else if (data.status == 1) {
                    layer.msg(data.msg, {
                        icon: 6
                    }, function() {
                        location.reload();
                    })
                }
            });
        });
    });
</script>
</body>
</html>