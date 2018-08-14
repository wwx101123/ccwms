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

class RegController extends BaseController {

    public function index() {

        if (IS_POST) {
            $data = I('post.');
            /*if (!$data['regProtocol']) {
                $this->ajaxReturn(array('status' => 0, 'msg' => '请先阅读并同意会员注册协义'));
            }*/
            if (zfCache('regInfo.grap_code') == 1) {
                $verify = new Verify();
                $verify_code = $data['verifyCode'];
                if (!$verify->check($verify_code, 'reg')) {
                    $this->ajaxReturn(array('status' => 0, 'msg' => '验证码错误'));
                }
            }
            if (zfCache('regInfo.grap_phone_code') == 1) {
                $res = smsCodeVerify($data['mobile'], $data['mobileCode'], session_id());
                if ($res['status'] != 1) {
                    $this->ajaxReturn(array('status' => 0, 'msg' => $res['msg']));
                }
            }

            // 验证身份证
            $Validate = new \Zfuwl\Logic\ValidateLogic();
            $res = $Validate->validateIdCard($data, $this->user_id);
            if ($res['status'] == -1) {
                $this->ajaxReturn(array('status' => 0, 'msg' => $res['msg']));
            }

            $model = new \Zfuwl\Logic\RegLogic();
            $res = $model->addUser($data);
            if ($res['status'] == 1) {
                $this->success('注册成功', U('User/userIndex'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $a = range(0, 9);
            $regAccountNum = rand(zfCache('regInfo.account_mai'), zfCache('regInfo.account_max'));
            $account = zfCache('regInfo.account_start');
            for ($i = 0; $i < zfCache('regInfo.account_max') - (strlen(zfCache('regInfo.account_start')) + strlen(date('s'))); $i++) {
                $account .= array_rand($a); // 拼接会员帐号
            }
            $account .= date('s');  // 生成会员帐号
            if (zfCache('regInfo.auto_account') == 1) {
                $this->assign('account', $account);
            }
            $tjrAccount = I('tjr'); // 推荐人账号
            $tjrAccount = htmlspecialchars($tjrAccount);
            $this->assign('tjrAccount', $tjrAccount);

            $levelList = M('level')->where(array('statu' => 1, 'reg' => 1))->cache('levelList')->select();
            $levelJsArr = array();
            foreach($levelList as $k=>$v) {
                $levelJsArr[$k] = array(
                    'value' => $v['level_id'],
                    'text' => $v['name_cn']
                );
            }

            $this->assign('levelJsStr', json_encode($levelJsArr));

            $this->regProtocol();
            $this->display('index');
        }
    }

    /**
     * 发送手机注册验证码
     */
    public function sendSmsRegCode() {
        $mobile = I('mobile');
        if (!checkMobile($mobile)) {
            exit(json_encode(array('status' => -1, 'msg' => '手机号码格式有误')));
        }
        if (zfCache('securityInfo.sendsms_is_imgcode') == 1) {
            $verify = new Verify();
            $verify_code = I('code');
            $verifyCheck = I('check_code', 'reg');
            if (!$verify->check($verify_code, $verifyCheck)) {
                exit(json_encode(array('status' => 0, 'msg' => '图形验证码错误')));
            }
        }
        //判断是否存在验证码
        $data = M('sms_log')->where(array('name' => $mobile, 'session_id' => session_id(), 'code' => array('neq', ''), 'is_verify' => 2))->order('id DESC')->find();
        //获取时间配置
        $smsTimeOut = zfCache('smtpSmsInfo.sms_time_out');
        $smsTimeOut = $smsTimeOut ? $smsTimeOut : 120;
        //120秒以内不可重复发送
        if ($data && (time() - $data['zf_time']) < $smsTimeOut) {
            exit(json_encode(array('status' => -1, 'msg' => zfCache('smtpSmsInfo.sms_time_out') / 60 . '分钟内不允许重复发送')));
        }
        $code = rand(1000, 9999);
        $time = zfCache('smtpSmsInfo.sms_time_out') / 60 . '分钟有效';
        $send = sendSms($mobile, '验证码:' . $code . '，' . $time, $code);
        if ($send['status'] != 1) {
            exit(json_encode(array('status' => -1, 'msg' => $send['msg'])));
        }
        exit(json_encode(array('status' => 1, 'msg' => '验证码已发送，请注意查收')));
    }

    /**
     * 发送邮箱验证码
     */
    public function sendEmailRegCode() {
        $email = I('email');
//        dump($check);die;

        if (!checkEmail($email)) {
            exit(json_encode(array('status' => -1, 'msg' => '邮箱格式有误')));
        }
        //判断是否存在验证码
        $data = M('sms_log')->where(array('name' => $email, 'session_id' => session_id(), 'code' => array('neq', ''), 'is_verify' => 2))->order('id DESC')->find();
        //获取时间配置
        $smsTimeOut = zfCache('smtpSmsInfo.email_time_out');
        $smsTimeOut = $smsTimeOut ? $smsTimeOut : 120;
        //120秒以内不可重复发送
        if ($data && (time() - $data['zf_time']) < $smsTimeOut) {
            exit(json_encode(array('status' => -1, 'msg' => zfCache('smtpSmsInfo.email_time_out') / 60 . '分钟内不允许重复发送')));
        }
        $code = rand(1000, 9999);
        $time = zfCache('smtpSmsInfo.email_time_out') / 60 . '分钟有效';
        $send = sendMail($email, $email, '注册', '验证码:' . $code . '，' . $time);
        if (!$send) {
            exit(json_encode(array('status' => -1, 'msg' => '发送失败')));
        }
        $data = array(
            'name' => $email,
            'zf_time' => time(),
            'content' => '验证码:' . $code . '，' . $time,
            'code' => $code,
            'is_type' => 2,
            'is_class' => 2,
            'session_id' => session_id()
        );
        M('sms_log')->add($data);
        exit(json_encode(array('status' => 1, 'msg' => '验证码已发送，请注意查收')));
    }

    /**
     * 会员注册协议
     */
    public function regProtocol() {
        $this->assign('regInfo', M('about')->where(array('is_type' => 1, 'about_type' => 1, 'cn' => 1))->find());
//        $this->display('regProtocol');
    }

    /**
     * 会员注册成功操作
     * @param array $user   会员信息
     */
    public function regSuccess($user, $post) {
        if (check_mobile($user['mobile'])) {
            if (zfCache('reg.cgprompt') != '') {
                zfsendSms($user['mobile'], '【' . zfCache('smtp_sms_info.autograph') . '】' . zfCache('reg.cgprompt') . '回T退订');
            }
        }
        if ($user['is_lock'] != 1) {
            // 注册状态为冻结就发送激活邮件
            $this->regSendEmail($post);
        }

        send_email($user['email'], '注册成功', '你的账号' . $user['account'] . '，登录密码：' . $post['password'] . '，交易密码：' . $post['secpwd']);
    }

    public function regSendEmail($data) {
        $str = '<p>尊敬的用户：</p><p>你好！</p><p>感谢您注册' . zfCache('zf_info.store_name') . '，请点击以下链接完成激活：<a href="http://' . $_SERVER["HTTP_HOST"] . U("Reg/validate", array("uid" => $data['user_id'], 'verify' => $data['verify'])) . '">http://' . $_SERVER["HTTP_HOST"] . U("Reg/validate", array("uid" => $data['user_id'], 'verify' => $data['verify'])) . '</a></p><p><hr />系统发信，请勿回信</p>';
        send_email($data['email'], zfCache('zf_info.store_name') . '注册激活邮件', $str);
    }

    public function validate() {
        $time = zfCache('smtp_sms_info.email_time') * 60;
        $user = M('users')->where(array('user_id' => $_GET['uid'], 'verify' => $_GET['verify']))->find();
        if (!$user) {
            $this->error('此链接已失效或已被使用');
        }
        if (($time + $user['reg_time']) < time()) {
            $this->error('此链接已失效或已被使用');
        }
        $res = M('users')->where(array('user_id' => $_GET['uid'], 'verify' => $_GET['verify']))->save(array('is_lock' => 1, 'verify' => '', 'is_email' => 1));
        if ($res) {
            $logic = new UsersLogic();
            session('user', $user);
            setcookie('user_id', $user['user_id'], null, '/');
            user_log($user['user_id'], '登录'); // 登录成功添加日志
            $this->success('激活成功', U('Users/index'));
        } else {
            $this->error('操作失败，请稍后再试');
        }
    }

    /**
     * 获取接点人账号
     */
    public function getJdrUser() {
        $tjrAccount = I('tjrAccount'); // 获取推荐人账号
        $tjrUser = M('users')->where(array('account' => $tjrAccount))->find();
        if (!$tjrUser) {
            $this->error('此推荐人不存在!');
        }
        $findAccount = $jdrAccount ? $jdrAccount : $tjrAccount; // 查找账号
        $jdrList = getJdrList($findAccount);
        $arr = array(
            '1' => 'A',
            '2' => 'B'
        );
        $str = '';
        foreach ($jdrList as $k => $v) {
            $str .= '<option value="' . $v['account'] . '">' . $arr[$k] . ' - ' . $v['account'] . '</option>';
        }

        $this->success($str);
        die;
    }

    /**
     * 判断接点人
     */
    public function checkJdr() {
        if (IS_AJAX) {
            $jdrAccount = I('post.jdrAccount');
            $pos = I('post.pos');

            $jdrInfo = M('users')->where(array('account' => $jdrAccount))->field('user_id')->find();
            if (!$jdrInfo) {
                $this->error('该安置人不存在!');
            }
            $jdrList = M('users_branch')->where(array('jdr_id' => $jdrInfo['user_id']))->select();
            if (count($jdrList) >= 2) {
                $this->error('该安置人下级已满!');
            }
            $jdrList = convertArrKey($jdrList, 'position');
            if ($jdrList[$pos] && $pos) {
                $this->error('此位置已注册!');
            }
            $this->success('成功!');
        }
    }

    /**
     * 判断接点人
     */
    public function checkJdrId() {
        if (IS_AJAX) {
            $jdrAccount = I('post.jdrAccount');
            $pos = I('post.pos');

            $jdrInfo = M('users')->where(array('user_id' => $jdrAccount))->field('user_id')->find();
            if (!$jdrInfo) {
                $this->error('该安置人不存在!');
            }
            $jdrList = M('users_branch')->where(array('jdr_id' => $jdrInfo['user_id']))->select();
            if (count($jdrList) >= 2) {
                $this->error('该安置人下级已满!');
            }
            $jdrList = convertArrKey($jdrList, 'position');
            if ($jdrList[$pos] && $pos) {
                $this->error('此位置已注册!');
            }
            $this->success('成功!');
        }
    }

    /**
     * 检测推荐人
     */
    public function issetTjr()
    {
        if(IS_POST) {
            $tjrAccount = I('account');
            $tjrAccount = htmlspecialchars($tjrAccount);

            $info = M('users')->where(['account' => $tjrAccount])->find();
            if(!$info) {
                exit(json_encode(['status' => -1, 'msg' => '推荐人不存在']));
            }

            # 推荐人数
            $tjrNum = M('users')->where(['tjr_id' => $info['user_id']])->count();
            if($tjrNum == 0) {
                $branchInfo = M('users_branch')->where(['uid' => $info['user_id']])->field('branch_id')->find();
                $id = jsPos($branchInfo['branch_id'], 1);
                $findBranchInfo = M('users_branch')->where(['branch_id' => $id])->field('uid')->find();
                $findUserInfo = M('users')->where(['user_id' => $findBranchInfo['uid']])->field('account')->find();

                if($findUserInfo['account'] != $tjrAccount) {
                    exit(json_encode(['status' => 1, 'msg' => ['jdr_account' => $findUserInfo['account']]]));
                } else {
                    exit(json_encode(['status' => 1, 'msg' => ['jdr_account' => $findUserInfo['account'], 'pos' => 1]]));
                }
            }
            if($tjrNum == 1) {
                $branchInfo = M('users_branch')->where(['uid' => $info['user_id']])->field('branch_id')->find();
                $id = jsPos($branchInfo['branch_id'], 2);
                $findBranchInfo = M('users_branch')->where(['branch_id' => $id])->field('uid')->find();
                $findUserInfo = M('users')->where(['user_id' => $findBranchInfo['uid']])->field('account')->find();

                if($findUserInfo['account'] != $tjrAccount) {
                    exit(json_encode(['status' => 1, 'msg' => ['jdr_account' => $findUserInfo['account']]]));
                } else {
                    exit(json_encode(['status' => 1, 'msg' => ['jdr_account' => $findUserInfo['account'], 'pos' => 2]]));
                }
            }
            exit(json_encode(['status' => 1, 'msg' => "ok"]));
        }
    }
    /**
     * 获取接点人
     */
    public function getRegion()
    {
        if(IS_POST && IS_AJAX) {
            $account = I('post.account');
            $account = htmlspecialchars($account);
            $user = M('users')->where(array('account' => $account))->field('user_id')->find();
            if(!$user) {
                $this->error('此安置人不存在!');
            }
            $userBranch = M('users_branch')->where(array('uid' => $user['user_id']))->field('br_num,branch_id')->find();
            $jdrs = M('users_branch')->where(['jdr_id' => $userBranch['branch_id']])->getField('position,uid');
            if(count($jdrs) >= $userBranch['br_num']) {
                $this->error('此接点人下级已满');
            }

            $str = '<option value="">--请选择区域--</option>';
            for($i = 1; $i <= $userBranch['br_num']; $i++) {
                if(!$jdrs[$i]) {
                    $str .= "<option value='{$i}'>".branchRegion($i)."</option>";
                }
            }
            $arr = array(
                'status' => 1,
                'str' => $str
            );
            $this->ajaxReturn($arr);
        }
    }

    /**
     * 验证码
     */
    public function verify() {
        $type = I('get.type') ? I('get.type') : 'reg';
        $config = array(
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
