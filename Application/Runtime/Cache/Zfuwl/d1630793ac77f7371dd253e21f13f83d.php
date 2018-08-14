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
</head>
<body>

  <div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md8">
            <div class="layui-row layui-col-space15">

                <div class="layui-col-md12">
                    <div class="layui-card">
                        <div class="layui-card-header">会员信息</div>
                        <div class="layui-card-body">

                            <div class="layui-carousel layadmin-carousel layadmin-backlog">
                                <div carousel-item>
                                    <ul class="layui-row layui-col-space10">
                                        <li class="layui-col-xs6">
                                            <a href="javascript:void(0);" class="layadmin-backlog-body">
                                                <h3 lay-href='<?php echo U("User/index");?>'>用户总量</h3>
                                                <p><cite><?php echo ($userTotal); ?></cite></p>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs6">
                                            <a href="javascript:;" class="layadmin-backlog-body">
                                                <h3 lay-href='<?php echo U("User/newsUser");?>'>今日新增</h3>
                                                <p><cite><?php echo ($newUsers); ?></cite></p>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs6">
                                            <a href="javascript:;" class="layadmin-backlog-body">
                                                <h3 lay-href='<?php echo U("User/trial");?>'>未审会员</h3>
                                                <p><cite><?php echo ($trialTotal); ?></cite></p>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs6">
                                            <a href="javascript:;" class="layadmin-backlog-body">
                                                <h3 lay-href='<?php echo U("User/lock");?>'>冻结会员</h3>
                                                <p><cite><?php echo ($lockTotal); ?></cite></p>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="layui-col-md12">
                    <div class="layui-card">
                        <div class="layui-card-header">数据概览</div>
                        <div class="layui-card-body">

                            <div class="layui-carousel layadmin-carousel layadmin-dataview" data-anim="fade" lay-filter="LAY-index-dataview">
                                <div carousel-item id="LAY-index-dataview">
                                    <div><i class="layui-icon layui-icon-loading1 layadmin-loading"></i></div>
                                    <!-- <div></div> -->
                                    <!-- <div></div> -->
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="layui-card">
                        <div class="layui-tab layui-tab-brief layadmin-latestData">
                            <ul class="layui-tab-title">
                                <li class="layui-this">登录日志</li>
                            </ul>
                            <div class="layui-tab-content">
                                <div class="layui-tab-item layui-show">
                                    <table class="layui-table" lay-even>
                                        <thead>
                                            <tr>
                                                <th>设备型号</th>
                                                <th>登录IP</th>
                                                <th>登录时间</th>
                                            </tr>
                                        </thead>
                                        <tbody class="layui-form">
                                            <?php if(count($adminLoglList) == 0): ?><tr align="center">
                                                    <td colspan="20">暂无数据</td>
                                                </tr>
                                                <?php else: ?>
                                                <?php if(is_array($adminLoglList)): foreach($adminLoglList as $k=>$v): ?><tr>
                                                        <td><?php echo ($v["equipment"]); ?></td>
                                                        <td><?php echo ($v["log_ip"]); ?></td>
                                                        <td><?php echo (date('Y-m-d H:i:s',$v["log_time"])); ?></td>
                                                    </tr><?php endforeach; endif; endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="layui-col-md4">
            <div class="layui-card">
              <div class="layui-card-header">系统信息</div>
              <div class="layui-card-body layui-text">
                <table class="layui-table">
                    <tbody>
                        <tr>
                            <td><strong>服务器系统</strong></td>
                            <td><?php echo ($sys_info["os"]); ?></td>
                        </tr>
                        <tr>
                            <td><strong>绑定域名</strong></td>
                            <td><?php echo ($sys_info["domain"]); ?></td>
                        </tr>
                        <tr>
                            <td><strong>服务器IP</strong></td>
                            <td> <?php echo ($sys_info["ip"]); ?></td>
                        </tr>
                        <tr>
                            <td><strong>PHP 版本</strong></td>
                            <td> <?php echo ($sys_info["phpv"]); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Mysql 版本</strong></td>
                            <td><?php echo ($sys_info["mysql_version"]); ?></td>
                        </tr>
                        <tr>
                            <td><strong>GD 版本</strong></td>
                            <td><?php echo ($sys_info["gdinfo"]); ?></td>
                        </tr>
                        <tr>
                            <td><strong>文件上传限制</strong></td>
                            <td><?php echo ($sys_info["fileupload"]); ?></td>
                        </tr>
                        <tr>
                            <td><strong>最大占用内存</strong></td>
                            <td><?php echo ($sys_info["memory_limit"]); ?></td>
                        </tr>
                        <tr>
                            <td><strong>最大执行时间</strong></td>
                            <td><?php echo ($sys_info["max_ex_time"]); ?></td>
                        </tr>
                        <tr>
                            <td><strong>安全模式</strong></td>
                            <td><?php echo ($sys_info["safe_mode"]); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Zlib支持</strong></td>
                            <td><?php echo ($sys_info["zlib"]); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Curl支持</strong></td>
                            <td><?php echo ($sys_info["curl"]); ?></td>
                        </tr>
                        <tr>
                            <td><strong>服务器环境</strong></td>
                            <td colspan="3"> <?php echo ($sys_info["web_server"]); ?> </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- <div class="layui-card">
            <div class="layui-card-header">效果报告</div>
            <div class="layui-card-body layadmin-takerates">
                <div class="layui-progress" lay-showPercent="yes">
                    <h3>转化率（日同比 28% <span class="layui-edge layui-edge-top" lay-tips="增长" lay-offset="-15"></span>）</h3>
                    <div class="layui-progress-bar" lay-percent="65%"></div>
                </div>
                <div class="layui-progress" lay-showPercent="yes">
                    <h3>签到率（日同比 11% <span class="layui-edge layui-edge-bottom" lay-tips="下降" lay-offset="-15"></span>）</h3>
                    <div class="layui-progress-bar" lay-percent="32%"></div>
                </div>
            </div>
        </div>

        <div class="layui-card">
            <div class="layui-card-header">实时监控</div>
            <div class="layui-card-body layadmin-takerates">
                <div class="layui-progress" lay-showPercent="yes">
                    <h3>CPU使用率</h3>
                    <div class="layui-progress-bar" lay-percent="58%"></div>
                </div>
                <div class="layui-progress" lay-showPercent="yes">
                    <h3>内存占用率</h3>
                    <div class="layui-progress-bar layui-bg-red" lay-percent="90%"></div>
                </div>
            </div>
        </div> -->

        <!-- <div class="layui-card">
            <div class="layui-card-header">产品动态</div>
            <div class="layui-card-body">
                <div class="layui-carousel layadmin-carousel layadmin-news" data-autoplay="true" data-anim="fade" lay-filter="news">
                    <div carousel-item>
                        <div><a href="http://fly.layui.com/docs/2/" target="_blank" class="layui-bg-red">layuiAdmin 快速上手文档</a></div>
                        <div><a href="javascript:;" onclick="layer.msg('等待添加')" target="_blank" class="layui-bg-green">layuiAdmin 集成心得分享</a></div>
                        <div><a href="javascript:;" onclick="layer.msg('等待添加')" target="_blank" class="layui-bg-blue">首款 layui 官方后台模板系统正式发布</a></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="layui-card">
            <div class="layui-card-header">
                作者心语
                <i class="layui-icon layui-icon-tips" lay-tips="要支持的噢" lay-offset="5"></i>
            </div>
            <div class="layui-card-body layui-text layadmin-text">
                <p>一直以来，layui 秉承无偿开源的初心，虔诚致力于服务各层次前后端 Web 开发者，在商业横飞的当今时代，这一信念从未动摇。即便身单力薄，仍然重拾决心，埋头造轮，以尽可能地填补产品本身的缺口。</p>
                <p>在过去的一段的时间，我一直在寻求持久之道，已维持你眼前所见的一切。而 layuiAdmin 是我们尝试解决的手段之一。我相信真正有爱于 layui 生态的你，定然不会错过这一拥抱吧。</p>
                <p>子曰：君子不用防，小人防不住。请务必通过官网正规渠道，获得 <a href="http://www.layui.com/admin/" target="_blank">layuiAdmin</a>！</p>
                <p>—— 贤心（<a href="http://www.layui.com/" target="_blank">layui.com</a>）</p>
            </div>
        </div> -->
    </div>

</div>
</div>

<script src="/Public/plugins/layuiadmin/layui/layui.js?t=1"></script>
<script>
  layui.config({
    base: '/Public/plugins/layuiadmin/' //静态资源所在路径
}).extend({
    index: 'lib/index' //主入口模块
}).use(['index', 'console', 'echarts','carousel'],function(){
        var e = layui.$,
            t = layui.carousel,
            a = layui.echarts,
            i = [],
            l = [/*{
                title: {
                    text: "今日流量趋势",
                    x: "center",
                    textStyle: {
                        fontSize: 14
                    }
                },
                tooltip: {
                    trigger: "axis"
                },
                legend: {
                    data: ["", ""]
                },
                xAxis: [{
                    type: "category",
                    boundaryGap: !1,
                    data: ["06:00", "06:30", "07:00", "07:30", "08:00", "08:30", "09:00", "09:30", "10:00", "11:30", "12:00", "12:30", "13:00", "13:30", "14:00", "14:30", "15:00", "15:30", "16:00", "16:30", "17:00", "17:30", "18:00", "18:30", "19:00", "19:30", "20:00", "20:30", "21:00", "21:30", "22:00", "22:30", "23:00", "23:30"]
                }],
                yAxis: [{
                    type: "value"
                }],
                series: [{
                    name: "PV",
                    type: "line",
                    smooth: !0,
                    itemStyle: {
                        normal: {
                            areaStyle: {
                                type: "default"
                            }
                        }
                    },
                    data: [111, 222, 333, 444, 555, 666, 3333, 33333, 55555, 66666, 33333, 3333, 6666, 11888, 26666, 38888, 56666, 42222, 39999, 28888, 17777, 9666, 6555, 5555, 3333, 2222, 3111, 6999, 5888, 2777, 1666, 999, 888, 777]
                }, {
                    name: "UV",
                    type: "line",
                    smooth: !0,
                    itemStyle: {
                        normal: {
                            areaStyle: {
                                type: "default"
                            }
                        }
                    },
                    data: [11, 22, 33, 44, 55, 66, 333, 3333, 5555, 12666, 3333, 333, 666, 1188, 2666, 3888, 6666, 4222, 3999, 2888, 1777, 966, 655, 555, 333, 222, 311, 699, 588, 277, 166, 99, 88, 77]
                }]
            }, {
                title: {
                    text: "访客浏览器分布",
                    x: "center",
                    textStyle: {
                        fontSize: 14
                    }
                },
                tooltip: {
                    trigger: "item",
                    formatter: "{a} <br/>{b} : {c} ({d}%)"
                },
                legend: {
                    orient: "vertical",
                    x: "left",
                    data: ["Chrome", "Firefox", "IE 8.0", "Safari", "其它浏览器"]
                },
                series: [{
                    name: "访问来源",
                    type: "pie",
                    radius: "55%",
                    center: ["50%", "50%"],
                    data: [{
                        value: 9052,
                        name: "Chrome"
                    }, {
                        value: 1610,
                        name: "Firefox"
                    }, {
                        value: 3200,
                        name: "IE 8.0"
                    }, {
                        value: 535,
                        name: "Safari"
                    }, {
                        value: 1700,
                        name: "其它浏览器"
                    }]
                }]
            }, */{
                title: {
                    text: "最近一周新增的用户量",
                    x: "center",
                    textStyle: {
                        fontSize: 14
                    }
                },
                tooltip: {
                    trigger: "axis",
                    formatter: "{b}<br>新增用户：{c}"
                },
                xAxis: [{
                    type: "category",
                    data: <?php echo ($userJsTime); ?>
                }],
                yAxis: [{
                    type: "value"
                }],
                series: [{
                    type: "line",
                    data: <?php echo ($userJsCount); ?>
                }]
            }],
            n = e("#LAY-index-dataview").children("div"),
            r = function(e) {
                i[e] = a.init(n[e], layui.echartsTheme), i[e].setOption(l[e]), window.onresize = i[e].resize
            };
        if (n[0]) {
            r(0);
            var o = 0;
            t.on("change(LAY-index-dataview)", function(e) {
                r(o = e.index)
            }), layui.admin.on("side", function() {
                setTimeout(function() {
                    r(o)
                }, 300)
            }), layui.admin.on("hash(tab)", function() {
                layui.router().path.join("") || r(o)
            })
        }
});
</script>
</body>
</html>