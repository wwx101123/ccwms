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
                <button type="button" class="layui-btn"><i class="layui-icon">&#xe628;</i> 会员等级管理</button>
                <button  type="button" class="layui-btn layui-btn-danger pull-right" onclick="history.go(-1);" style="float:right;"><i class="layui-icon">&#xe603;</i> 返回</button>
                <div style="clear:both;"></div>
            </div>
            <div class="layui-field-box">
                <form class="layui-form layui-form-pane" action="">
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">会员账号</label>
                            <div class="layui-input-inline"> <input type="text"value="<?php echo ($userInfo['account']); ?>" readonly="" placeholder="会员账号"  autocomplete="off" class="layui-input" lay-verify="required"></div>
                        </div>
                        <div class="layui-inline">
                            <label class="layui-form-label">注册时间</label>
                            <div class="layui-input-inline">
                                <input type="text" value="<?php echo (date('Y-m-d H:i:s', $userInfo["jh_time"])); ?>"  readonly="" placeholder="英文名称" autocomplete="off" class="layui-input" lay-verify="required"/>
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">当前等级</label>
                            <div class="layui-input-inline">
                                <input type="text" value="<?php echo ($levelInfo[$userInfo[level]]); ?>"  readonly="" placeholder="当前等级" autocomplete="off" class="layui-input" />
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">请选择等级</label>
                        <div class="layui-input-inline">
                            <select name="level_id" lay-search="">
                                <option value="0">--请选择--</option>
                                <?php if(is_array($levelInfo)): foreach($levelInfo as $k=>$v): ?><option value="<?php echo ($k); ?>" <?php if($k == $userInfo['level']): ?>selected<?php endif; ?>><?php echo ($v); ?></option><?php endforeach; endif; ?>
                            </select>
                        </div>
                         <div class="layui-form-mid layui-word-aux"><b style="color:red;">不选择或者选择为空即表示取消</b></div>
                    </div>
                   <div class="layui-form-item layui-form-text">
                        <label class="layui-form-label"><b style="color: red;">操作备注</b></label>
                        <div class="layui-input-block">
                          <textarea name="note" placeholder="操作备注 调整的原因" class="layui-textarea" lay-verify="required"></textarea>
                        </div>
                    </div>
                    <fieldset class="layui-elem-field site-demo-button" style="margin-top: 30px;">
                        <legend>注意事项</legend>
                        <div>本次升级或者降级操作 不会产生任何的奖金；也不影响拨出K值，以及对会员的收支影响，</div>
                        <div style="color:red;"><b>但升级后的级别，所有的奖项计划按升级后的级别统一执行</b></div>
                    </fieldset>
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <input type="hidden" name="user_id" value="<?php echo ($userInfo['user_id']); ?>" />
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