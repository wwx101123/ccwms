<?php

namespace Mobile\Controller;

class ForgotController extends BaseController {

    public function _initialize() {
        parent::_initialize();
    }

    public function forgotIndex() {
        $this->display('forgotIndex');
    }

    /**
     * 手机号找回密码
     */
    public function forgotByMobile()
    {

        if (IS_POST) {
            $data = I('post.');
            if($data['account'] == '') {
                $this->error('请输入手机账号');
            }

            $user = M('users')->where(['account' => $data['account']])->find();

            if (!$user) {
                $this->error('此手机账号不存在!');
            }

            if($data['new_pass'] == '') {
                $this->error('请输入新密码!');
            }
            if(!checkPass($data['new_pass'])) {
                $this->error('新密码格式不正确!');
            }
            if ($data['new_pass'] != $data['confirm_new_pass']) {
                $this->error('两次密码输入不一致');
            }
            if(!checkMobile($data['account'])) {
                $this->error('请输入正确的手机号!');
            }
            if (!$data['mobile_code']) {
                $this->error('请输入短信验证码');
            } else {
                $res = smsCodeVerify($data['account'], $data['mobile_code'], session_id());
                if ($res['status'] != 1) {
                    $this->error($res['msg']);
                }
                $model = new \Zfuwl\Logic\UserLogic();
                $res = $model->mobilePassword($data);
                if ($res['status'] == 1) {
                    $this->success('操作成功' . $res['msg'], U('Login/index'));
                    exit;
                } else {
                    $this->error('操作失败,' . $res['msg']);
                }
            }
        } else {
            $this->error('操作失败');
        }
    }

}
