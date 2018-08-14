layui.use(['layer'], function() {});
function chekloginSubmit() {
    var username = $.trim($('#username').val());
    if (username == '') {
        showErrorMsg('请输入帐号');
        return;
    }
    var password = $.trim($('#password').val());
    if (password == '') {
        showErrorMsg('请输入密码');
        return;
    }
    var referurl = $('#referurl').val();
    var verify_code = $.trim($('#verify_code').val());
    if (verify_code == '' && $("#is_verify").val() == 1) {
        showErrorMsg('请输入验证码');
        return;
    }
    $.ajax({
        type: 'post',
        url: '/index.php?m=Member&c=login&a=doLogin&t=' + Math.random(),
        data: {username: username, password: password, referurl: referurl, verify_code: verify_code},
        dataType: 'json',
        success: function (res) {
            if (res.status == 1) {
                 layer.msg('登录成功', {icon:1}, function(){
                    window.location.href = res.url;
                });
            } else {
                checkIsYzm();
                showErrorMsg(res.msg);
                verify();
            }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            showErrorMsg('网络失败，请刷新页面后重试');
        }
    })
}
function checkIsYzm()
{
    $.ajax({
        type:'post',
        url:'/index.php?m=Member&c=login&a=checkWhetherVerify&t=' + Math.random(),
        success:function(data){
            if(data == 1) {
                $('#is_verify').val(1);
                $("#yzm").show();
            }
        },
        error:function(){
            layer.msg('网络错误!', {icon:5});
        }
    })
}
checkIsYzm();
function chekMobileloginSubmit() {
    var username = $.trim($('#username').val());
    if (username == '') {
        showErrorMsg('请输入帐号');
        return;
    }
    var password = $.trim($('#password').val());
    if (password == '') {
        showErrorMsg('请输入密码');
        return;
    }
    var referurl = $('#referurl').val();
    var verify_code = $.trim($('#verify_code').val());
    if (verify_code == '') {
        showErrorMsg('请输入验证码');
        return;
    }
    $.ajax({
        type: 'post',
        url: '/index.php?m=Mobile&c=login&a=doLogin&t=' + Math.random(),
        data: {username: username, password: password, referurl: referurl, verify_code: verify_code},
        dataType: 'json',
        success: function (res) {
            if (res.status == 1) {
                layer.msg('登录成功', {icon:1}, function(){
                    window.location.href = res.url;
                });
            } else {
                showErrorMsg(res.msg);
                verify();
            }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            showErrorMsg('网络失败，请刷新页面后重试');
        }
    })
}
function chekloginSubmitEn() {
    var username = $.trim($('#username').val());
    if (username == '') {
        showErrorMsg('Please enter an account number');
        return;
    }
    var password = $.trim($('#password').val());
    if (password == '') {
        showErrorMsg('Please input a password');
        return;
    }
    var referurl = $('#referurl').val();
    var verify_code = $.trim($('#verify_code').val());
    if (verify_code == '' && $("#is_verify").val() == 1) {
        showErrorMsg('Please enter the verification code');
        return;
    }
    $.ajax({
        type: 'post',
        url: '/index.php?m=En&c=login&a=doLogin&t=' + Math.random(),
        data: {username: username, password: password, referurl: referurl, verify_code: verify_code},
        dataType: 'json',
        success: function (res) {
            if (res.status == 1) {
                 layer.msg('Login successful', {icon:1}, function(){
                    window.location.href = res.url;
                });
            } else {
                checkIsYzm();
                showErrorMsg(res.msg);
                verify();
            }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            showErrorMsg('Network failed, please retry after refreshing the page');
        }
    })
}
function showErrorMsg(msg) {
    $('#prompt').html(msg);
}
function verify() {
    $('#verify_code_img').click();
}
document.onkeydown = function (event) {
    e = event ? event : (window.event ? window.event : null);
    if (e.keyCode == 13) {
        chekloginSubmit();
    }
}