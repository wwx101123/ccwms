<?php

namespace Mobile\Controller;

use Zfuwl\Logic\ValidateLogic;

class ValidateController extends CommonController {

    protected $validateLogic;

    public function _initialize() {
        parent::_initialize();

        $this->validateLogic = new ValidateLogic();
    }

    /**
     * 验证会员手机号
     */
    public function validateMobile() {
        if (IS_POST) {
            $post = I("post.");

            $res = $this->validateLogic->validateMobile($post, $this->user_id);

            if ($res['status'] == 1) {
                $this->success($res['msg'], U('User/securityInfo'));
            } else {
                $this->error($res['msg']);
            }
        } else {
            $this->display('validateMobile');
        }
    }

    /**
     * 验证会员邮箱账号
     */
    public function validateEmail() {
        if (IS_POST) {
            $post = I('post.');

            $res = $this->validateLogic->validateEmail($post, $this->user_id);

            if ($res['status'] == 1) {
                $this->success($res['msg'], U('User/securityInfo'));
            } else {
                $this->error($res['msg']);
            }
        } else {
            $this->display('validateEmail');
        }
    }

    /**
     * 验证会员身份证
     */
    public function validateIdCard() {
        if (IS_POST) {
            $post = I('post.');

            $res = $this->validateLogic->validateIdCard($post, $this->user_id);

            if ($res['status'] == 1) {
                $this->success($res['msg'], U('User/securityInfo'));
            } else {
                $this->error($res['msg']);
            }
        } else {
            $this->display('validateIdCard');
        }
    }

    /**
     * 验证会员银行卡信息
     */
    public function validateBank() {
        if (IS_POST) {
            $post = I('post.');
            $res = $this->validateLogic->validateBank($post, $this->user_id);
            if ($res['status'] == 1) {
                $this->success($res['msg'], U('Validate/validateBank'));
            } else {
                $this->error($res['msg']);
            }
        } else {

            $bankList = M('bank')->where(array('statu' => 1, 'is_t' => 1))->group('name_cn')->field('name_cn,id')->select();
            $bankList = convertArrKey($bankList, 'id');

            $jsArr = array();
            $i = 0;
            foreach ($bankList as $k => $v) {
                $jsArr[$i] = array(
                    'value' => $v['id'],
                    'text' => $v['name_cn']
                );
                $i++;
            }
            $this->assign('jsStr', json_encode($jsArr));
            $this->assign('bankList', $bankList);
            $this->assign('bankInfo', M('bank')->where("statu=1")->cache('bankInfo')->getField('id,name_cn'));
            $this->assign('userBank', D("UserView")->where(array('user_id' => $this->user_id))->field('account,bank_address,username,opening_id,bank_account,bank_name,zfb_name,wx_name,ylh_name,yft_name,yhy_name')->find());


            $this->display('validateBank');
        }
    }

}
