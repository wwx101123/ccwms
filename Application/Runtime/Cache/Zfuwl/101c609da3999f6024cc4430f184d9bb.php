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
                <form class="layui-form layui-form-pane" action="">
                    <div class="layui-form-item">
                         <div class="layui-inline layui-form-item" pane="">
                            <label class="layui-form-label">系统状态</label>
                            <div class="layui-input-block">
                                <input type="radio" name="is_systec_test" value="1" title="正常运行中" <?php if($config["is_systec_test"] == 1): ?>checked=""<?php endif; ?>>
                                <input type="radio" name="is_systec_test" value="2" title="测试运行中" <?php if($config["is_systec_test"] == 2): ?>checked=""<?php endif; ?>>
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">数据备份包</label>
                        <div class="layui-input-inline">
                            <input type="text" name="back_size" value="<?php echo ((isset($config['back_size']) && ($config['back_size'] !== ""))?($config['back_size']):'10240'); ?>" placeholder="数据备份包大小" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid layui-word-aux"><b style="color:red;">KB/个</b></div>
                    </div>
                    <div class="layui-form-item">
                         <div class="layui-inline layui-form-item" pane="">
                            <label class="layui-form-label">数据备份后</label>
                            <div class="layui-input-block">
                                <input type="radio" name="is_mysql" value="1" title="发送邮箱" <?php if($config["is_mysql"] == 1): ?>checked=""<?php endif; ?>>
                                <input type="radio" name="is_mysql" value="2" title="禁止发送" <?php if($config["is_mysql"] == 2): ?>checked=""<?php endif; ?>>
                            </div>
                        </div>
                        <div class="layui-inline">
                            <label class="layui-form-label">接收邮箱号</label>
                            <div class="layui-input-inline"><input type="text" name="mysql_email" value="<?php echo ($config['mysql_email']); ?>" autocomplete="off" placeholder="邮箱账号" class="layui-input"></div>
                        </div>
                    </div>


                    <div class="layui-form-item">
                        <label class="layui-form-label">最高总K值</label>
                        <div class="layui-input-inline">
                            <input type="text" name="bocu_total" value="<?php echo ((isset($config['bocu_total']) && ($config['bocu_total'] !== ""))?($config['bocu_total']):'70'); ?>" placeholder="会员未操作" autocomplete="off" class="layui-input">
                            <div class="layui-form-mid layui-word-aux">0 为不限制</div>
                        </div>
                        <div class="layui-form-mid layui-word-aux"><b style="color:red;">% 后暂停发放奖金</b></div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">最高日K值</label>
                        <div class="layui-input-inline">
                            <input type="text" name="day_bocu_total" value="<?php echo ((isset($config['day_bocu_total']) && ($config['day_bocu_total'] !== ""))?($config['day_bocu_total']):'70'); ?>" placeholder="会员未操作" autocomplete="off" class="layui-input">
                            <div class="layui-form-mid layui-word-aux">0 为不限制</div>
                        </div>
                        <div class="layui-form-mid layui-word-aux"><b style="color:red;">% 后暂停发放奖金</b></div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">会员未操作</label>
                        <div class="layui-input-inline">
                            <input type="text" name="web_past_due_time" value="<?php echo ((isset($config['web_past_due_time']) && ($config['web_past_due_time'] !== ""))?($config['web_past_due_time']):'3'); ?>" placeholder="会员未操作" autocomplete="off" class="layui-input">
                            <div class="layui-form-mid layui-word-aux">0 为不控制时间</div>
                        </div>
                        <div class="layui-form-mid layui-word-aux"><b style="color:red;">M 后自动退出</b></div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">管理未操作</label>
                        <div class="layui-input-inline">
                            <input type="text" name="admin_past_due_time" value="<?php echo ((isset($config['admin_past_due_time']) && ($config['admin_past_due_time'] !== ""))?($config['admin_past_due_time']):'3'); ?>" placeholder="管理未操作" autocomplete="off" class="layui-input">
                            <div class="layui-form-mid layui-word-aux">0 为控制时间</div>
                        </div>
                        <div class="layui-form-mid layui-word-aux"><b style="color:red;">M 后自动退出</b></div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">同登录密码</label>
                        <div class="layui-input-inline">
                            <input type="text" name="edit_password_num" value="<?php echo ((isset($config['edit_password_num']) && ($config['edit_password_num'] !== ""))?($config['edit_password_num']):'10'); ?>" placeholder="同登录密码修改次数" autocomplete="off" class="layui-input">
                            <div class="layui-form-mid layui-word-aux">0 为不限制修改次数</div>
                        </div>
                        <div class="layui-form-mid layui-word-aux"><b style="color:red;">次最多修改</b></div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">同二级密码</label>
                        <div class="layui-input-inline">
                            <input type="text" name="edit_secpwd_num" value="<?php echo ((isset($config['edit_secpwd_num']) && ($config['edit_secpwd_num'] !== ""))?($config['edit_secpwd_num']):'10'); ?>" placeholder="同二级密码修改次数" autocomplete="off" class="layui-input">
                            <div class="layui-form-mid layui-word-aux">0 为不限制修改次数</div>
                        </div>
                        <div class="layui-form-mid layui-word-aux"><b style="color:red;">次最多修改</b></div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">密码格式</label>
                            <div class="layui-input-inline">
                                  <input type="text" name="pass_mai" value="<?php echo ((isset($config['pass_mai']) && ($config['pass_mai'] !== ""))?($config['pass_mai']):'3'); ?>" placeholder="密码长度" autocomplete="off" class="layui-input">
                            </div>
                            <div class="layui-form-mid">位至 - </div>
                            <div class="layui-input-inline">
                                  <input type="text" name="pass_max" value="<?php echo ((isset($config['pass_max']) && ($config['pass_max'] !== ""))?($config['pass_max']):'6'); ?>" placeholder="密码长度" autocomplete="off" class="layui-input">
                            </div>
                            <div class="layui-form-mid">位</div>
                        </div>
                    </div>
                    <div class="layui-inline layui-form-item" pane="">
                        <label class="layui-form-label">找回密码</label>
                        <div class="layui-input-block">
                            <input type="radio" name="is_forgot" value="1" title="黙认手机验证码找回" <?php if($config["is_forgot"] == 1): ?>checked=""<?php endif; ?>>
                            <input type="radio" name="is_forgot" value="2" title="黙认邮箱验证码找回" <?php if($config["is_forgot"] == 2): ?>checked=""<?php endif; ?>>
                            <input type="radio" name="is_forgot" value="3" title="黙认密保问题找回" <?php if($config["is_forgot"] == 3): ?>checked=""<?php endif; ?>>
                        </div>
                    </div>
                     <div class="layui-form-item layui-form-text">
                        <label class="layui-form-label">密保问题，多值请用“｜”号分开</label>
                        <div class="layui-input-block">
                            <textarea name="security" placeholder="请输入密保问题" class="layui-textarea"><?php echo ((isset($config['security']) && ($config['security'] !== ""))?($config['security']):'你最喜欢的城市｜你最喜欢的音乐｜你人生的梦想'); ?></textarea>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label" style="width:170px;">发送短信图形验证码</label>
                        <div class="layui-input-block">
                            <input type="radio" name="sendsms_is_imgcode" value="1" title="启用" <?php if($config["sendsms_is_imgcode"] == 1): ?>checked=""<?php endif; ?>>
                            <input type="radio" name="sendsms_is_imgcode" value="2" title="不启用" <?php if($config["sendsms_is_imgcode"] == 2): ?>checked=""<?php endif; ?>>
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">验证银行卡</label>
                        <div class="layui-input-block">
                          <input type="text" name="bankAppKey" value="<?php echo ($config['bankAppKey']); ?>" autocomplete="off" placeholder="请输入银行卡三元素检测AppKey" class="layui-input">
                          <div class="layui-form-mid layui-word-aux">0 为不开启验证；请输入银行卡三元素检测AppKey,申请地址：https://www.juhe.cn/docs/api/id/207</div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">身份证实名</label>
                        <div class="layui-input-block">
                          <input type="text" name="cardAppKey" value="<?php echo ($config['cardAppKey']); ?>" autocomplete="off" placeholder="请输入身份证实名认证AppKey" class="layui-input">
                          <div class="layui-form-mid layui-word-aux">0 为不开启验证；请输入身份证实名认证AppKey,申请地址：https://www.juhe.cn/docs/api/id/103</div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                         <div class="layui-inline layui-form-item" pane="">
                            <label class="layui-form-label">身份证实名</label>
                            <div class="layui-input-block">
                                <input type="radio" name="card_img" value="1" title="上传身份证图片" <?php if($config["card_img"] == 1): ?>checked=""<?php endif; ?>>
                                <input type="radio" name="card_img" value="2" title="关闭上传" <?php if($config["card_img"] == 2): ?>checked=""<?php endif; ?>>
                            </div>
                        </div>
                    </div>
         
               <!--     <div class="layui-form-item">
                        <label class="layui-form-label">储存释放</label>
                        <div class="layui-input-inline">
                            <select name="sh_mid" lay-search="">
                                <option value="0">&#45;&#45;请选择&#45;&#45;</option>
                                <?php if(is_array($moneyInfo)): foreach($moneyInfo as $k=>$v): ?><option value="<?php echo ($k); ?>" <?php if($k == $config['sh_mid']): ?>selected<?php endif; ?>><?php echo ($v); ?></option><?php endforeach; endif; ?>
                            </select>
                        </div>
                        <div class="layui-form-mid layui-word-aux"><b style="color:red;"></b></div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">积分转出用</label>
                        <div class="layui-input-inline">
                            <select name="zc_mid" lay-search="">
                                <option value="0">&#45;&#45;请选择&#45;&#45;</option>
                                <?php if(is_array($moneyInfo)): foreach($moneyInfo as $k=>$v): ?><option value="<?php echo ($k); ?>" <?php if($k == $config['zc_mid']): ?>selected<?php endif; ?>><?php echo ($v); ?></option><?php endforeach; endif; ?>
                            </select>
                        </div>
                        <div class="layui-form-mid layui-word-aux"><b style="color:red;"></b></div>
                    </div>
-->
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">领取积分</label>
                            <div class="layui-input-inline">
                                <input type="text" name="shi_money" value="<?php echo ((isset($config['shi_money']) && ($config['shi_money'] !== ""))?($config['shi_money']):'30000'); ?>" placeholder="领取积分金额" autocomplete="off" class="layui-input">
                            </div>
                            <div class="layui-form-mid">以下按每日</div>
                            <div class="layui-input-inline">
                                <input type="text" name="shi_pera" value="<?php echo ((isset($config['shi_pera']) && ($config['shi_pera'] !== ""))?($config['shi_pera']):'5'); ?>" placeholder="领取积分比例" autocomplete="off" class="layui-input">
                            </div>
                            <div class="layui-form-mid">%，否则按</div>
                            <div class="layui-input-inline">
                                <input type="text" name="shi_perb" value="<?php echo ((isset($config['shi_perb']) && ($config['shi_perb'] !== ""))?($config['shi_perb']):'2'); ?>" placeholder="领取积分比例" autocomplete="off" class="layui-input">
                            </div>
                            <div class="layui-form-mid">%</div>
                        </div>
                    </div>
					<div class="layui-form-item">
                        <div class="layui-inline layui-form-item" pane="">
                            <label class="layui-form-label">一键回本</label>
                            <div class="layui-input-block">
                                <input type="radio" name="returnben" value="1" title="开启" <?php if($config["returnben"] == 1): ?>checked=""<?php endif; ?>>
                                <input type="radio" name="returnben" value="2" title="关闭" <?php if($config["returnben"] == 2): ?>checked=""<?php endif; ?>>
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <button class="layui-btn" id="submitBtn" lay-submit lay-filter="articleHandle">立即提交</button>
                            <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                        </div>
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
        layui.use(['layer', 'form', 'upload', 'laydate'], function () {
            var form = layui.form, $ = layui.jquery;
            form.on('submit(articleHandle)', function (data) {
                var ArticleInfo = data.field;
                var url = "<?php echo U('');?>";
                $.post(url, ArticleInfo, function (data) {
                    if (data.status != 1) {
                        layer.msg(data.msg, {icon: 5});
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