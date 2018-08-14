<?php

namespace Mobile\Controller;

use Think\Controller;

class CommonController extends BaseController {

    public function _initialize() {
        parent::_initialize();
        $this->session_id = session_id(); // 当前的 session_id
        define('SESSION_ID', $this->session_id); // 将当前的session_id保存为常量，供其它方法调用
        if (session('?user')) {
            unset($_SESSION['url']);
            $user = session('user');
            $user = M('users')->where("user_id = {$user['user_id']}")->find();
            if ($user['data_id'] > 0) {
                $this->assign('userDataInfo', M('users_data')->where(array('id' => $user['data_id']))->find());
            }

            $this->user_id = $user['user_id'];
            $this->user = $user;
            if ($user['frozen'] != 1) {
                $this->logout();
            }
            $out_time = zfCache('securityInfo.web_past_due_time') == '' ? 1000 : zfCache('securityInfo.web_past_due_time') * 60;
            if (($_SESSION['web_past_due_time'] + $out_time) <= time()) {
                session('is_show', 0);
                unset($_SESSION['user']);
                $this->error('身份已过期，请重新登录', U('Login/index'));
            }
            $this->tgurl = "http://" . $_SERVER["HTTP_HOST"] . U("Reg/index", array("tjr" => $user['account'])); // 获取推广地址并加密
            session('web_past_due_time', time());
        } else {
            header("location:" . U('Mobile/Login/index'));
            exit();
        }
      	//leaderS($this->user_id);
      	//leaderMinus($this->user_id);
        // $this->assign('adminlist', M('admin_user')->cache('adminUser')->getField('admin_id,user_name'));
        $this->assign('levelInfo', M('level')->where("statu=1")->cache('levelInfo')->getField('level_id,name_cn'));
        $this->assign('leaderInfo', M('leader')->where("statu=1")->getField('id,name_cn'));
      	$this->assign('bankList', M('bank')->where("statu=1")->getField('id,name_cn'));
        $this->assign('userData', D("UserView")->where(['user_id' => $this->user_id])->field('head')->find());

    }

    public function oppositeAccount() {
        $account = I('name');
        $user = M('users')->where("account='{$account}' OR mobile='{$account}' OR email='{$account}'")->field('nickname,mobile')->find();
        if ($user) {
            $user['cg'] = 1;
        } else {
            $user['cg'] = 0;
        }
        $this->ajaxReturn($user);
    }

    public function logout() {
        session('is_show', 0);
        setcookie('user_id', '', time() - 3600, '/');
        unset($_SESSION['user']);
        header("location:" . U('Login/index'));
        exit();
    }

}
