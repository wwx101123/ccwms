<?php

namespace zfuwl\Controller;

use Think\Controller;

class LoginController extends Controller {

    public function _initialize() {
        if (session('?admin_id')) {
            $this->redirect('Zfuwl/Index/index');
        }
    }

    /**
     * 登录页面显示
     */
    public function login() {
        $this->assign('config', zfCache('webInfo'));
        $this->display();
    }

    /**
     * 用户登录操作
     */
    public function doLogin() {
        $userName = I('post.name');
        $pass = I('post.pwd');
        $verifyCode = I('post.verifyCode');
        if (!$userName) {
            $this->ajaxReturn(array('status' => -1, 'msg' => '请输入帐号！'));
        }
        if (!$pass) {
            $this->ajaxReturn(array('status' => -1, 'msg' => '请输入密码！'));
        }
        if (!$verifyCode) {
            $this->ajaxReturn(array('status' => -1, 'msg' => '请输入验证码！'));
        }
        $verify = new \Think\Verify();
        if (!$verify->check($verifyCode, 'admin_login')) {
            $this->ajaxReturn(array('status' => -1, 'msg' => '验证码输入错误！'));
        }

        $where = array(
            'user_name' => $userName,
            'password' => adminEncrypt($pass),
            'status' => 1
        );
        $adminUserModel = D('AdminUser');
        $userInfo = $adminUserModel->findUser($where);
        if (!$userInfo) {
            $this->ajaxReturn(array('status' => -1, 'msg' => '帐号或密码不正确！'));
        }
        session('admin_past_due_time', time());
        session('admin_id', $userInfo['admin_id']);
        session('admin_name', $userInfo['user_name']);
        session('admin_time', time());
        $adminUserModel->updateLastTime($userInfo['admin_id']); // 更新最后一次登录信息
        D('AdminLog')->addAdminLog($userInfo['admin_id'], '登录后台',1); // 添加管理员登录记录
        $this->ajaxReturn(array('status' => 1, 'msg' => '登录成功'));
    }

    /**
     * 生成验证码
     */
    public function verify() {
        $config = array(
            'fontSize' => 16, // 验证码字体大小(px)
            'codeSet' => '0123456789',
            'length' => 4, // 验证码位数
            'imageH' => 37, // 验证码图片高度
            'imageW' => 130, // 验证码图片宽度
            'fontttf' => '5.ttf', // 验证码字体，不设置随机获取
            'useCurve' => false, // 是否画混淆曲线
            'useNoise' => false, // 是否添加杂点   true
            'bg' => array(220, 220, 220), // 背景颜色
        );
        $Verify = new \Think\Verify($config);
        $Verify->entry("admin_login");
    }

}
