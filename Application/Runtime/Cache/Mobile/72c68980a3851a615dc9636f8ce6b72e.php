<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>

<head>
    <title>登录</title>
    <meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
<title></title>
<link rel="stylesheet" href="/Public/plugins/font-awesome/css/font-awesome.min.css" media="all">
<link rel="stylesheet" type="text/css" href="/Template/Mobile/default/Static/css/mui.min.css" />
<link rel="stylesheet" type="text/css" href="/Template/Mobile/default/Static/css/style.css" />
<link rel="stylesheet" type="text/css" href="/Template/Mobile/default/Static/css/mui.showLoading.css" />
<script src="/Template/Mobile/default/Static/js/jquery.min.js" type="text/javascript" charset="utf-8"></script>
</head>
<style>
    body {background: #1F242A;}
</style>
<body>


<div class="boxesHei">
	<div class="logoBox"><img src="<?php echo ((isset($config['webInfo_web_logo_img']) && ($config['webInfo_web_logo_img'] !== ""))?($config['webInfo_web_logo_img']):'/Public/images/not_head.jpg'); ?>" alt="" /></div>
    <div class="headlineBox">源码链管理平台登录</div>
    <div id="item1" class="inputBox">
        <form>
            <div class="parentInput">
                <span class="spanLabel"><i class="fa fa-mobile i"></i></span>
                <input type="text" name="username" autocomplete="off" class="mui-input-clear blicInput" placeholder="请输入手机号">
            </div>
            <div class="parentInput">
                <span class="spanLabel"><i class="fa fa-lock i"></i></span>
                <input type="password" name="password" autocomplete="off" class="mui-input-password blicInput" placeholder="请输入登录密码">
            </div>
            <div class="parentInput">
                <span class="spanLabel"><i class="fa fa-key i"></i></span>
                <input type="text" name="verify_code" autocomplete="off" class="mui-input-clear blicInput" placeholder="请输入验证码">
                <img src="<?php echo U('Login/verify', array('type' => 'login_index'));?>" onclick="refreshVerify();" class="yzmStyle imgYzm"/>
            </div>
            <div>
                <button type="button" class="pushButton accountLogin">登陆</button>
            </div>
        </form>
    </div>
    <div class="interlinkageBox">
        <a href="<?php echo U('Forgot/forgotIndex');?>" class="inLinkA inLinkAbr">忘记密码？</a>
        <a href="<?php echo U('Reg/index');?>" class="inLinkA">立即注册</a>
    </div>

</div>
<script src="/Template/Mobile/default/Static/js/mui.min.js" type="text/javascript" charset="utf-8"></script>
<script src="/Template/Mobile/default/Static/js/mui.showLoading.js" type="text/javascript" charset="utf-8"></script>
<script src="/Template/Mobile/default/Static/js/style.js" type="text/javascript" charset="utf-8"></script>
<script>
    var mask=mui.createMask();
    var imgObj = [];
    mui('body').on('change', '.imgup', function(){
        var formData = new FormData();
        var img = $(this).val();
        formData.append("img", $(this)[0].files[0]);
        formData.append("field", 'img');
        mui.showLoading("上传中");
        $.ajax({
            url: "<?php echo U('Zfuwl/Api/imageUp');?>",
            type: "post",
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            async:false,
            success: function(res) {
                mask.close();//关闭遮罩层
                mui.hideLoading();
                if(res.code != 0) {
                    mui.alert(res.msg);
                } else {
                    $.post("<?php echo U('User/saveUserHeadImg');?>",{src:res.data.src}, function(data){
                        if(data.status == 1) {
                            mui.toast('上传成功',{ duration:'2000', type:'div' });
                            $(imgObj).attr('src', res.data.src);
                        } else {
                            mui.toast(data.msg,{ duration:'2000', type:'div' });
                        }
                    });
                }
            },
            error: function(e) {
                alert("网络错误，请重试！！");
            }
        });
    });

    mui('body').on('tap', '.uploadImgHead', function(){
        $('.imgup').click();
        imgObj = $(this);
    });
</script>
<script src="/Public/js/zfuwl.js"></script>
<script>
    var mask=mui.createMask();
    var referurl = "<?php echo ($referurl); ?>";
    mui('body').on('tap', '.accountLogin', function () {
        var obj = $(this);
        var username = $("input[name='username']").val();
        var verify_code = $("input[name='verify_code']").val();
        var password = $("input[name='password']").val();
        mui.closePopup();
        if (username == '') {
            return mui.toast('请输入账号', {duration: 'short', type: 'div'});
        }
        if (verify_code == '') {
            return mui.toast('请输入验证码', {duration: 'short', type: 'div'});
        }
        if (password == '') {
            return mui.toast('请输入密码', {duration: 'short', type: 'div'});
        }
        $(obj).attr('disabled', 'true');
        mui.showLoading("登录中","div");
        mask.show();//显示遮罩层
        $.ajax({
            type: 'post',
            url: '<?php echo U("Login/doLogin");?>',
            data: {username: username, password: password, referurl: referurl, verify_code: verify_code},
            dataType: 'json',
            success: function (res) {
                mask.close();//关闭遮罩层
                mui.hideLoading();
                if (res.status == 1) {
                    mui.toast('登录成功', {duration: '2000', type: 'div'});
                    setTimeout(function () {
                        mui.openWindow({
                            id: res.url,
                            url: res.url
                        });
                    }, 2000);
                } else {
                    refreshVerify();
                    $(obj).removeAttr('disabled');
                    return mui.toast(res.msg, {duration: '2000', type: 'div'});
                }
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                return mui.toast('网络失败，请刷新页面后重试', {duration: '2000', type: 'div'});
            }
        });
    });
    mui('body').on('tap', '.mobileLogin', function () {
        var obj = $(this);
        var mobile = $("input[name='mobile']").val();
        var mobile_code = $("input[name='mobile_code']").val();
        if(mobile == '') {
            return mui.toast('请输入手机号', {duration:'2000', type:'div'});
        }
        if(!checkMobile(mobile)) {
            return mui.toast('请输入正确的手机号', {duration:'2000', type:'div'});
        }
        if(mobile_code == '') {
            return mui.toast('请输入手机验证码', {duration:'2000', type:'div'});
        }
        $(obj).attr('disabled', 'true');
        mui.showLoading("登录中","div");
        mask.show();//显示遮罩层
        $.ajax({
            type: 'post',
            url: '<?php echo U("Login/doLogin2");?>',
            data: {mobile: mobile, mobile_code: mobile_code, referurl: referurl},
            dataType: 'json',
            success: function (res) {
                mask.close();//关闭遮罩层
                mui.hideLoading();
                if (res.status == 1) {
                    mui.toast('登录成功', {duration: '2000', type: 'div'});
                    setTimeout(function () {
                        mui.openWindow({
                            id: res.url,
                            url: res.url
                        });
                    }, 2000);
                } else {
                    refreshVerify();
                    $(obj).removeAttr('disabled');
                    return mui.toast(res.msg, {duration: '2000', type: 'div'});
                }
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                return mui.toast('网络失败，请刷新页面后重试', {duration: '2000', type: 'div'});
            }
        });
    });

    function refreshVerify() {
        $('.imgYzm').attr('src', '<?php echo U("Login/verify", array("type" => "login_index"));?>');
    }

    var smsObj = [];
    // 发送手机短信
    function send_sms_reg_code(obj){
        smsObj = obj;
        var mobile = $('#mobile').val();
        if(!checkMobile(mobile)){
            mui.toast('请输入正确的手机号码',{ duration:'long', type:'div' });
            return;
        }
        <?php if($config['securityInfo_sendsms_is_imgcode'] == 1): ?>var code = $('#verifyCode').val();
        var data = {mobile:mobile,code:code,check_code:'login_index'};
        <?php else: ?>
        var data = {mobile:mobile};<?php endif; ?>
        var url = "<?php echo U('Reg/sendSmsRegCode');?>";
        mui.showLoading("正在发送","div");
        mask.show();//显示遮罩层
        $.post(url, data, function(data){
            mask.close();//关闭遮罩层
            mui.hideLoading();
            obj = $.parseJSON(data);
            if(obj.status == 1){
                $(smsObj).attr("disabled","disabled");
                intAs = <?php echo ((isset($config["smtpSmsInfo_sms_time_out"]) && ($config["smtpSmsInfo_sms_time_out"] !== ""))?($config["smtpSmsInfo_sms_time_out"]):'120'); ?>; // 手机短信超时时间
                jsInnerTimeout(intAs);
                mui.toast(obj.msg,{ duration:'long', type:'div' });
            } else {
                mui.toast(obj.msg,{ duration:'long', type:'div' });
            }
        });
    }
    //倒计时函数
    function jsInnerTimeout(intAs){
        intAs--;
        if(intAs<=-1){
            $(smsObj).removeAttr("disabled");
            $(smsObj).text("获取验证码");
            return true;
        }
        $(smsObj).text(intAs+'秒');
        setTimeout("jsInnerTimeout("+intAs+")",1000);
    };
</script>
</body>

</html>