<?php

/**
 *  众福网络直销系统管理软件
 * ============================================================================
 * 版权所有 2015-2027 深圳市众福网络软件有限公司，并保留所有权利。
 * 网站地址: http://www.zfuwl.com   http://www.jiafuw.com
 * 联系方式：qq:1845218096 电话：15899929162
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Author:  众福团队
 * Date:2016-12-10 20:27  100
 */

namespace Mobile\Controller;

use Think\Controller;
use Think\Verify;

class LoginController extends BaseController {

    public function index() {
        if (session('?user')) {
            header('location:' . U('Mobile/User/userIndex'));
        }
        if ($_SESSION['url'] != '') {
            $referurl = U('/Mobile/User/' . $_SESSION['url']);
        } else {
            $referurl = U("/Mobile/User/userIndex");
        }
        $referurl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : U('Mobile/User/userIndex');
        $this->assign('referurl', $referurl);

        $this->display('index');
//        $this->display('index_' . zfCache('loginInfo.login'));
    }

    /**
     * 判断是否需要验证码
     * @return [type] [description]
     */
    public function checkWhetherVerify() {
        if (session('yzm')) {
            echo 1;
        } else {
            echo 2;
        }
    }

    public function doLogin() {
        $username = I('post.username');
        $password = I('post.password');
        $username = trim($username);
        $password = trim($password);
        $verify_code = I('post.verify_code');
        $verify = new Verify();
        if (!$verify->check($verify_code, 'login_index')) {
            $res = array(
                'status' => 0,
                'msg' => '验证码错误'
            );
            exit(json_encode($res));
        }
        
        $logic = new \Common\Logic\LoginLogic();
        $res = $logic->login($username, $password);
        if ($res['status'] == 1) {
            $res['url'] = urldecode(I('post.referurl'));
            session('user', $res['result']);
            session('web_past_due_time', time());
            setcookie('user_id', $res['result']['user_id'], null, '/');
        }
        exit(json_encode($res));
    }

    public function doLogin2() {
        $mobile = I('post.mobile');
        $mobileCode = I('post.mobile_code');
        if(!checkMobile($mobile)) {
            exit(json_encode(array('status' => -1, 'msg' => '请输入正确的手机号')));
        }
        if($mobileCode == '') {
            exit(json_encode(array('status' => -1, 'msg' => '请输入手机验证码')));
        }
        $res = smsCodeVerify($mobile, $mobileCode, session_id());
        if ($res['status'] != 1) {
            exit(json_encode(array('status' => 0, 'msg' => $res['msg'])));
        }
        $info = M('users_data')->where(array('mobile' => $mobile))->find();
        if(!$info) {
            exit(json_encode(array('status' => -1, 'msg' => '此手机号不存在')));
        }
        $user = M('users')->where(array('data_id' => $info['id']))->find();
        if(!$user || $user['frozen'] != 1) {
            exit(json_encode(array('status' => -1, 'msg' => '此账号已被冻结')));
        }

        session('user', $user);
        session('web_past_due_time', time());
        setcookie('user_id', $user['user_id'], null, '/');
        exit(json_encode(array('status' => 1, 'msg' => '登陆成功', 'url' => urldecode(I('post.referurl')))));
    }

    /**
     * 验证码
     */
    public function verify() {
        $type = I('get.type') ? I('get.type') : 'login_index';
        $config = array(
            'codeSet' => '0123456789', // 全数字
            'fontSize' => 15, // 验证码字体大小(px)
            'length' => 4, // 验证码位数
            'imageH' => 40, // 验证码图片高度
            'imageW' => 113, // 验证码图片宽度
            'fontttf' => '4.ttf', // 验证码字体，不设置随机获取
            'useCurve' => false, // 是否画混淆曲线
            'useNoise' => false
                ) // 是否添加杂点 true
        ;
        $Verify = new Verify($config);
        $Verify->entry($type);
    }

}
