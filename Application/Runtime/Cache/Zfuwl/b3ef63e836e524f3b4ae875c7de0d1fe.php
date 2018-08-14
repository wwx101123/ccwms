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
            <div class="layui-field-box">
                <form class="layui-form">
                    <blockquote class="layui-elem-quote layui-text">邮件参数设置</blockquote>
                    <div class="layui-form-item">
                        <label class="layui-form-label">系统邮箱</label>
                        <div class="layui-input-inline">
                            <select name="is_smtp" id="is_smtp">
                                <option value="1" <?php if($config['is_smtp'] == 1): ?>selected<?php endif; ?>>开启</option>
                                <option value="2" <?php if($config['is_smtp'] == 2): ?>selected<?php endif; ?>>关闭</option>
                            </select>
                        </div>
                        <div class="layui-inline">
                            <label class="layui-form-label">服务器</label>
                            <div class="layui-input-inline">
                                <input type="text" name="smtp_server" value="<?php echo ($config['smtp_server']); ?>" placeholder="发送邮箱的smtp地址" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-inline">
                            <label class="layui-form-label">端口</label>
                            <div class="layui-input-inline">
                                <input type="text" name="smtp_port" value="<?php echo ($config['smtp_port']); ?>" placeholder="smtp的端口。默认为25" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-inline">
                            <label class="layui-form-label">邮箱账号</label>
                            <div class="layui-input-inline">
                                <input type="text" name="smtp_user" value="<?php echo ($config['smtp_user']); ?>" placeholder="发送邮箱" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-inline">
                            <label class="layui-form-label">邮箱密码</label>
                            <div class="layui-input-inline">
                                <input type="text" name="smtp_pwd" value="<?php echo ($config['smtp_pwd']); ?>" placeholder="发送邮箱密码" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">发件人姓名</label>
                        <div class="layui-input-inline">
                            <input type="text" name="send_username" value="<?php echo ($config['send_username']); ?>" placeholder="发件人姓名" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-inline">
                            <label class="layui-form-label">发件人邮箱</label>
                            <div class="layui-input-inline">
                                <input type="text" name="send_useremail" value="<?php echo ($config['send_useremail']); ?>" placeholder="发件人邮箱" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-inline">
                            <label class="layui-form-label">回复人姓名</label>
                            <div class="layui-input-inline">
                                <input type="text" name="reply_username" value="<?php echo ($config['reply_username']); ?>" placeholder="回复人姓名" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-inline">
                            <label class="layui-form-label">回复人邮箱</label>
                            <div class="layui-input-inline">
                                <input type="text" name="reply_useremail" value="<?php echo ($config['reply_useremail']); ?>" placeholder="回复人邮箱" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-inline">
                            <label class="layui-form-label">有效时间</label>
                            <div class="layui-input-inline">
                                <select name="email_time_out" id="sms_time_out">
                                    <option value="60" <?php if($config["email_time_out"] == 60): ?>selected<?php endif; ?>>60 s</option>
                                    <option value="120" <?php if($config["email_time_out"] == 120): ?>selected<?php endif; ?>>120 s</option>
                                    <option value="300" <?php if($config["email_time_out"] == 300): ?>selected<?php endif; ?>>300 s</option>
                                    <option value="600" <?php if($config["email_time_out"] == 600): ?>selected<?php endif; ?>>600 s</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">测试邮箱</label>
                        <div class="layui-input-inline">
                            <input type="text" name="test_email" value="<?php echo ($config['test_email']); ?>" placeholder="测试邮箱" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-inline">
                            <a href="javaScript:void(0);" class="testSend">
                                <button type="button" class="layui-btn" style="float:left;">测试发送</button>
                            </a>
                            <div class="layui-form-mid layui-word-aux" style="margin-left:10px;"><b>首次请先保存配置再测试</b></div>
                        </div>
                    </div>

                    <blockquote class="layui-elem-quote layui-text">短信参数设置</blockquote>

                    <div class="layui-form-item">
                        <label class="layui-form-label">是否启用短信</label>
                        <div class="layui-input-inline">
                            <select name="is_sms" id="is_sms">
                                <option value="1" <?php if($config['is_sms'] == 1): ?>selected<?php endif; ?>>开启</option>
                                <option value="2" <?php if($config['is_sms'] == 2): ?>selected<?php endif; ?>>关闭</option>
                            </select>
                        </div>
                        <div class="layui-inline">
                            <label class="layui-form-label">用户名</label>
                            <div class="layui-input-inline">
                                <input type="text" name="sms_user" value="<?php echo ($config['sms_user']); ?>" placeholder="短信用户名" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-inline">
                            <label class="layui-form-label">key</label>
                            <div class="layui-input-inline">
                                <input type="text" name="sms_key" value="<?php echo ($config['sms_key']); ?>" placeholder="短信key" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-inline">
                            <label class="layui-form-label">短信数量</label>
                            <div class="layui-input-inline">
                                <input type="text" id="smsNum" disabled class="layui-input">
                            </div>
                        </div>
                        <div class="layui-inline">
                            <label class="layui-form-label">有效时间</label>
                            <div class="layui-input-inline">
                                <select name="sms_time_out" id="sms_time_out">
                                    <option value="60" <?php if($config["sms_time_out"] == 60): ?>selected<?php endif; ?>>60 s</option>
                                    <option value="120" <?php if($config["sms_time_out"] == 120): ?>selected<?php endif; ?>>120 s</option>
                                    <option value="300" <?php if($config["sms_time_out"] == 300): ?>selected<?php endif; ?>>300 s</option>
                                    <option value="600" <?php if($config["sms_time_out"] == 600): ?>selected<?php endif; ?>>600 s</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item" style="text-align:center;">
                        <div class="layui-inline"><button class="layui-btn" lay-submit lay-filter="saveSmtpSms">提交</button></div>
                    </div>
                </form>
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
                layui.use(['form'], function (obj) {
                    var form = layui.form, $ = layui.jquery;

                    //监听提交
                    form.on('submit(saveSmtpSms)', function (data) {
                        var postData = data.field;
                        var url = "<?php echo U('Website/smtpSmsInfo');?>";
                        $.post(url, postData, function (data) {
                            if (data.status != 1) {
                                layer.msg(data.msg, {icon: 5});
                                return;
                            } else {
                                layer.msg(data.msg, {icon: 6, time: 2000}, function () {
                                    location.reload();
                                });
                            }
                        });
                        return false; //阻止表单跳转
                    });

                    $('.testSend').click(function () {
                        $.ajax({
                            type: "POST",
                            url: "<?php echo U('Website/testSendEmail');?>",
                            success: function (data) {
                                if (data.status == 0) {
                                    layer.msg(data.msg, {icon: 5});
                                    return;
                                } else if (data.status == 1) {
                                    layer.msg(data.msg, {icon: 6});
                                }
                            }
                        });
                    });
                    $(document).ready(function () {
                        getSmsNum();
                    });

                    function getSmsNum()
                    {
                        $.ajax({
                            type: 'get',
                            url: '<?php echo U("index/getSmsNum");?>',
                            success: function (data) {
                                if (data.status < 1) {
                                    layer.msg(data.msg, {icon: 5});
                                } else {
                                    $('#smsNum').val(data.msg);
                                }
                            }
                        }, 'json');
                    }
                });
            </script>
            </body>
            </html>