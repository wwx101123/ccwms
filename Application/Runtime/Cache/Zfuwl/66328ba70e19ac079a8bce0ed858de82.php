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
    <style>
        /* 案例 */
        .fly-case-header{position: relative; height: 260px; text-align: center; background: #393D49;}
        .fly-case-year{position: absolute; top: 30px; width: 100%; line-height: 50px; font-size: 50px; text-align: center; color: #fff; font-weight: 300;}
        .fly-case-banner{position: absolute; left: 50%; top: 100px; width: 670px; margin-left: -335px;}
        .fly-case-btn{position: absolute; bottom: 30px; left: 0; width: 100%; text-align: center;}
        .fly-case-btn a{color: #fff;}
        .fly-case-btn .layui-btn-primary{background: none; color: #fff;}
        .fly-case-btn .layui-btn-primary:hover{border-color: #5FB878;}
        .fly-case-tab{margin-top: 20px; text-align: center;}
        .fly-case-tab span,
        .fly-case-tab span a{border-color: #009688;}
        .fly-case-tab .tab-this{background-color: #009688; color: #fff;}
        .fly-case-list{margin-top: 15px; font-size: 0;}
        .fly-case-list li,
        .layer-ext-ul li{display: inline-block; vertical-align: middle; *display: inline; *zoom:1; font-size: 14px; background-color: #fff;}
        .fly-case-list{width: 100%;}
        .fly-case-list li{width: 239px; margin: 0 15px 15px 0; padding: 10px;}
        .fly-case-list li:hover{box-shadow: 1px 1px 5px rgba(0,0,0,.1);}
        .fly-case-img{position: relative; display: block;}
        .fly-case-img img{width: 239px; height: 150px;}
        .fly-case-img .layui-btn{display: none; position: absolute; bottom: 20px; left: 50%; margin-left: -29px;}
        .fly-case-img:hover .layui-btn{display: inline-block;}
        .fly-case-list li h2{padding: 10px 0 5px; line-height: 22px; font-size: 18px; white-space: nowrap; overflow: hidden; text-align: center;}
        .fly-case-desc{height: 60px; line-height: 20px; font-size: 12px; color: #666; overflow: hidden;}
        .fly-case-info{position: relative; margin: 10px 0 0; padding: 10px 65px 0 45px; border-top: 1px dotted #eee;}
        .fly-case-info p{height:24px; line-height: 24px;}
        .fly-case-user{position: absolute; left: 0; top: 15px; width: 35px; height: 35px;}
        .fly-case-user img{width: 35px; height: 35px; border-radius: 100%;}
        .fly-case-info .layui-btn{position: absolute; right: 0; top: 15px;  padding: 0 15px;}
        .layer-ext-ul{margin: 10px; max-height: 500px;}
        .layer-ext-ul img{width: 50px; height: 50px; border-radius: 100%;}
        .layer-ext-ul li{margin: 8px;}
        .layer-ext-case .layui-layer-title{border: none; background-color: #009688; color: #fff;}
    </style>
</head>
<body>
    <div class="admin-main">
        <fieldset class="layui-elem-field">
            <div class="layui-field-box">
                <form class="layui-form layui-form-pane" action="">


                    <div class="layui-form-item">
                         <div class="layui-inline layui-form-item" pane="">
                            <label class="layui-form-label">登录开关</label>
                            <div class="layui-input-block">
                                <input type="radio" name="web_kg" value="1" title="正常登录" <?php if($config["web_kg"] == 1): ?>checked=""<?php endif; ?>>
                                <input type="radio" name="web_kg" value="2" title="禁止登录" <?php if($config["web_kg"] == 2): ?>checked=""<?php endif; ?>>
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-mid layui-word-aux" ><b style="color:red;">开启【 正常登录 】的情况下  以下参数才生效</b></div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">星期一</label>
                        <div class="layui-input-inline">
                              <input type="text" name="monday_1_add" value="<?php echo ($config['monday_1_add']); ?>" placeholder="H" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid"> - </div>
                        <div class="layui-input-inline">
                              <input type="text" name="monday_1_out" value="<?php echo ($config['monday_1_out']); ?>" placeholder="H" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid">H</div>
                        <div class="layui-input-inline">
                            <input type="radio" name="monday_1" value="1" title="允许会员登录" <?php if($config["monday_1"] == 1): ?>checked=""<?php endif; ?>>
                            <input type="radio" name="monday_1" value="2" title="不限制登录时间" <?php if($config["monday_1"] == 2): ?>checked=""<?php endif; ?>>
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">星期二</label>
                        <div class="layui-input-inline">
                          <input type="text" name="monday_2_add" value="<?php echo ($config['monday_2_add']); ?>"  placeholder="H" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid"> - </div>
                        <div class="layui-input-inline">
                              <input type="text" name="monday_2_out" value="<?php echo ($config['monday_2_out']); ?>" placeholder="H" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid">H</div>
                        <div class="layui-input-inline">
                            <input type="radio" name="monday_2" value="1" title="允许会员登录" <?php if($config["monday_2"] == 1): ?>checked=""<?php endif; ?>>
                            <input type="radio" name="monday_2" value="2" title="不限制登录时间" <?php if($config["monday_2"] == 2): ?>checked=""<?php endif; ?>>
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">星期三</label>
                        <div class="layui-input-inline">
                          <input type="text" name="monday_3_add" value="<?php echo ($config['monday_3_add']); ?>"  placeholder="H" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid"> - </div>
                        <div class="layui-input-inline">
                              <input type="text" name="monday_3_out" value="<?php echo ($config['monday_3_out']); ?>" placeholder="H" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid">H</div>
                        <div class="layui-input-inline">
                            <input type="radio" name="monday_3" value="1" title="允许会员登录" <?php if($config["monday_3"] == 1): ?>checked=""<?php endif; ?>>
                            <input type="radio" name="monday_3" value="2" title="不限制登录时间" <?php if($config["monday_3"] == 2): ?>checked=""<?php endif; ?>>
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">星期四</label>
                        <div class="layui-input-inline">
                          <input type="text" name="monday_4_add" value="<?php echo ($config['monday_4_add']); ?>"  placeholder="H" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid"> - </div>
                        <div class="layui-input-inline">
                              <input type="text" name="monday_4_out" value="<?php echo ($config['monday_4_out']); ?>" placeholder="H" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid">H</div>
                        <div class="layui-input-inline">
                            <input type="radio" name="monday_4" value="1" title="允许会员登录" <?php if($config["monday_4"] == 1): ?>checked=""<?php endif; ?>>
                            <input type="radio" name="monday_4" value="2" title="不限制登录时间" <?php if($config["monday_4"] == 2): ?>checked=""<?php endif; ?>>
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">星期五</label>
                        <div class="layui-input-inline">
                          <input type="text" name="monday_5_add" value="<?php echo ($config['monday_5_add']); ?>"  placeholder="H" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid"> - </div>
                        <div class="layui-input-inline">
                              <input type="text" name="monday_5_out" value="<?php echo ($config['monday_5_out']); ?>" placeholder="H" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid">H</div>
                        <div class="layui-input-inline">
                            <input type="radio" name="monday_5" value="1" title="允许会员登录" <?php if($config["monday_5"] == 1): ?>checked=""<?php endif; ?>>
                            <input type="radio" name="monday_5" value="2" title="不限制登录时间" <?php if($config["monday_5"] == 2): ?>checked=""<?php endif; ?>>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">星期六</label>
                        <div class="layui-input-inline">
                          <input type="text" name="monday_6_add" value="<?php echo ($config['monday_6_add']); ?>"  placeholder="H" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid"> - </div>
                        <div class="layui-input-inline">
                              <input type="text" name="monday_6_out" value="<?php echo ($config['monday_6_out']); ?>" placeholder="H" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid">H</div>
                        <div class="layui-input-inline">
                            <input type="radio" name="monday_6" value="1" title="允许会员登录" <?php if($config["monday_6"] == 1): ?>checked=""<?php endif; ?>>
                            <input type="radio" name="monday_6" value="2" title="不限制登录时间" <?php if($config["monday_6"] == 2): ?>checked=""<?php endif; ?>>
                        </div>
                    </div>

                   <div class="layui-form-item">
                        <label class="layui-form-label">星期日</label>
                        <div class="layui-input-inline">
                          <input type="text" name="monday_7_add" value="<?php echo ($config['monday_7_add']); ?>"  placeholder="H" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid"> - </div>
                        <div class="layui-input-inline">
                              <input type="text" name="monday_7_out" value="<?php echo ($config['monday_7_out']); ?>" placeholder="H" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid">H</div>
                        <div class="layui-input-inline">
                            <input type="radio" name="monday_7" value="1" title="允许会员登录" <?php if($config["monday_7"] == 1): ?>checked=""<?php endif; ?>>
                            <input type="radio" name="monday_7" value="2" title="不限制登录时间" <?php if($config["monday_7"] == 2): ?>checked=""<?php endif; ?>>
                        </div>
                    </div>
                    <div class="layui-form-item layui-form-text">
                        <label class="layui-form-label">禁止登录原因</label>
                        <div class="layui-input-block">
                            <textarea name="kgcontent" placeholder="请输入禁止登录原因" class="layui-textarea"><?php echo ($config['kgcontent']); ?></textarea>
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
                var form = layui.form, $ = layui.jquery,upload = layui.upload;
                upload.render({
                    elem: '.uploadImg',
                    url:'<?php echo U("Api/imageUp");?>',
                    before: function(){
                    }
                    ,done: function(res, index, upload){
                        $('#'+this.data.field).val(res.data.src);
                        $('.'+this.data.field).attr('src', res.data.src);
                    }
                });
                $('#submitBtn').click(function () {
                    editor.sync();
                });
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