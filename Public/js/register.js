layui.use(['carousel', 'element', 'form', 'layedit', 'laydate'], function() {
	var laydate = layui.laydate;
	var carousel = layui.carousel;
	var form = layui.form,
		layer = layui.layer,
		layedit = layui.layedit,
		laydate = layui.laydate;


});

function checkForm() {
		var nametip = checkUserName();
		var passtip = checkPassword();
		var conpasstip = ConfirmPassword();
		var conpasstip_2 = ConfirmPassword_2();
		var phonetip = checkPhone();
		var emailtip = checkEmail();
		var phonetip_2 = checkPhoneXGJ();
		var phonetip_3 = checkPhoneXGX();
		var emailtip_2 = checkEmailXGJ();
		var emailtip_3 = checkEmailXGX();
		return nametip && passtip && conpasstip && conpasstip_2 && phonetip && emailtip && phonetip_2 && phonetip_3 && emailtip_2 && emailtip_3;
	}
	//验证用户名   

function checkUserName() {
		var username = document.getElementById('userName');
		var errname = document.getElementById('nameErr');
		var pattern = /^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,10}$/; //用户名格式正则表达式：用户名6-10位由数字和字母混合组成
		if (username.value.length == 0) {
			errname.innerText = "用户名不能为空"
			errname.className = "error"
			return false;
		}
		if (!pattern.test(username.value)) {
			errname.innerText = "用户名6-10位由数字和字母混合组成!"
			errname.className = "error"
			return false;
		} else {
			errname.innerText = "OK"
			errname.className = "success";
			return true;
		}
	}
	//验证密码   

function checkPassword() {
		var userpasswd = document.getElementById('userPasword');
		var errPasswd = document.getElementById('passwordErr');
		var pattern = /^[a-zA-Z]\w{5,17}$/; //密码格式正则表达式：以字母开头，长度在6~18之间，只能包含字符、数字和下划线
		if (!pattern.test(userpasswd.value)) {
			errPasswd.innerHTML = "字母开头，6-18位由字母或数字和字母组成！"
			errPasswd.className = "error"
			return false;
		} else {
			errPasswd.innerHTML = "OK"
			errPasswd.className = "success";
			return true;
		}
	}
	//确认密码 

function ConfirmPassword() {
	var userpasswd = document.getElementById('userPasword');
	var userConPassword = document.getElementById('userConfirmPasword');
	var errConPasswd = document.getElementById('conPasswordErr');
	if ((userpasswd.value) != (userConPassword.value) || userConPassword.value.length == 0) {
		errConPasswd.innerHTML = "上下密码不一致"
		errConPasswd.className = "error"
		return false;
	} else {
		errConPasswd.innerHTML = "OK"
		errConPasswd.className = "success";
		return true;
	}
}

//二级密码   

function checkPassword_2() {
		var userpasswd_2 = document.getElementById('userPasword_2');
		var errPasswd_2 = document.getElementById('passwordErr_2');
		var pattern_2 = /^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,18}$/; //密码格式正则表达式：以字母开头，长度在6~18之间，只能包含字符、数字和下划线
		if (!pattern_2.test(userpasswd_2.value)) {
			errPasswd_2.innerHTML = "二级密码格式：6-18位由数字和字母混合组成！"
			errPasswd_2.className = "error"
			return false;
		} else {
			errPasswd_2.innerHTML = "OK"
			errPasswd_2.className = "success";
			return true;
		}
	}
	//确认二级密码 

function ConfirmPassword_2() {
	var userpasswd_2 = document.getElementById('userPasword_2');
	var userConPassword_2 = document.getElementById('userConfirmPasword_2');
	var errConPasswd_2 = document.getElementById('conPasswordErr_2');
	if ((userpasswd_2.value) != (userConPassword_2.value) || userConPassword_2.value.length == 0) {
		errConPasswd_2.innerHTML = "上下密码不一致"
		errConPasswd_2.className = "error"
		return false;
	} else {
		errConPasswd_2.innerHTML = "OK"
		errConPasswd_2.className = "success";
		return true;
	}
}

//验证手机号 

function checkPhone() {
	var userphone = document.getElementById('userPhone');
	var phonrErr = document.getElementById('phoneErr');
	var pattern = /^1[34578]\d{9}$/; //验证手机号正则表达式 
	if (!pattern.test(userphone.value)) {
		phonrErr.innerHTML = "请输入有效的手机号码！"
		phonrErr.className = "error"
		return false;
	} else {
		phonrErr.innerHTML = "OK"
		phonrErr.className = "success";
		return true;
	}
}

//邮箱账号 

function checkEmail() {
	var userEmail = document.getElementById('userEmail');
	var EmailErr = document.getElementById('EmailErr');
	var zhiEmail = /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/; //验证邮箱账号正则表达式 
	if (!zhiEmail.test(userEmail.value)) {
		EmailErr.innerHTML = "请输入有效的邮箱账号！"
		EmailErr.className = "error"
		return false;
	} else {
		EmailErr.innerHTML = "OK"
		EmailErr.className = "success";
		return true;
	}
}


//手机号码修改(旧)

function checkPhoneXGJ() {
    var userphone = document.getElementById('userPhoneXGJ');
    var phonrErr = document.getElementById('phoneErrXGJ');
    var pattern = /^1[34578]\d{9}$/; //验证手机号正则表达式
    if (!pattern.test(userphone.value)) {
        phonrErr.innerHTML = "请输入有效的原手机号码！"
        phonrErr.className = "error"
        return false;
    } else {
        phonrErr.innerHTML = "OK"
        phonrErr.className = "success";
        return true;
    }
}
//手机号码修改(新)

function checkPhoneXGX() {
    var userphone = document.getElementById('userPhoneXGX');
    var phonrErr = document.getElementById('phoneErrXGX');
    var pattern = /^1[34578]\d{9}$/; //验证手机号正则表达式
    if (!pattern.test(userphone.value)) {
        phonrErr.innerHTML = "请输入有效的新手机号码！"
        phonrErr.className = "error"
        return false;
    } else {
        phonrErr.innerHTML = "OK"
        phonrErr.className = "success";
        return true;
    }
}

//邮箱账号

function checkEmailXGJ() {
    var userEmail = document.getElementById('userEmailXGJ');
    var EmailErr = document.getElementById('EmailErrXGJ');
    var zhiEmail = /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/; //验证邮箱账号正则表达式
    if (!zhiEmail.test(userEmail.value)) {
        EmailErr.innerHTML = "请输入有效的原邮箱账号！"
        EmailErr.className = "error"
        return false;
    } else {
        EmailErr.innerHTML = "OK"
        EmailErr.className = "success";
        return true;
    }
}

//邮箱账号

function checkEmailXGX() {
    var userEmail = document.getElementById('userEmailXGX');
    var EmailErr = document.getElementById('EmailErrXGX');
    var zhiEmail = /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/; //验证邮箱账号正则表达式
    if (!zhiEmail.test(userEmail.value)) {
        EmailErr.innerHTML = "请输入有效的新邮箱账号！"
        EmailErr.className = "error"
        return false;
    } else {
        EmailErr.innerHTML = "OK"
        EmailErr.className = "success";
        return true;
    }
}