<?php

namespace Zfuwl\Logic;

use Zfuwl\Model\CommonModel;

class ValidateLogic extends CommonModel {

    protected $tableName = 'users_data';

    /**
     * 验证会员手机号
     * @param array $post 会员提交的数据
     * @param int $userId 会员id
     * @return array
     */
    public function validateMobile($post, $userId) {
        if ($post['new_mobile'] == '') {
            return array('status' => -1, 'msg' => '请输入手机号');
        }
        if (!checkMobile($post['new_mobile'])) {
            return array('status' => -1, 'msg' => '请输入正确的手机号');
        }
        $user = D("UserView")->where(array('user_id' => $userId))->field("mobile,is_mobile")->find();
        if ($user['is_mobile'] == 1 && $user['mobile'] == $post['new_mobile']) {
            return array('status' => -1, 'msg' => '新手机号不能与原手机号一样');
        }
        if ($post['mobile_code'] == '') {
            return array('status' => -1, 'msg' => '请输入手机验证码');
        }

        if ($user['is_mobile'] == 1) {
            $res = smsCodeVerify($user['mobile'], $post['mobile_code'], session_id());
        } else {
            $res = smsCodeVerify($post['new_mobile'], $post['mobile_code'], session_id());
        }
        if ($res['status'] != 1) {
            return $res;
        }

        $data = array(
            'is_mobile' => 1,
            'mobile' => $post['new_mobile']
        );
        $res = (new UserLogic())->saveUserInfo($data, $userId);
        return $res;
    }

    /**
     * 验证会员邮箱
     * @param array $post 会员提交的数据
     * @param int $userId 会员id
     * @return array
     */
    public function validateEmail($post, $userId) {
        if ($post['new_email'] == '') {
            return array('status' => -1, 'msg' => '请输入邮箱账号');
        }
        if (!checkEmail($post['new_email'])) {
            return array('status' => -1, 'msg' => '请输入正确的邮箱账号');
        }
        $user = D("UserView")->where(array('user_id' => $userId))->field("email,is_email")->find();
        if ($user['is_email'] == 1 && $user['email'] == $post['new_email']) {
            return array('status' => -1, 'msg' => '新邮箱不能与原邮箱一样');
        }
        if ($post['email_code'] == '') {
            return array('status' => -1, 'msg' => '请输入邮箱验证码');
        }

        if ($user['is_email'] == 1) {
            $res = emailCodeVerify($user['email'], $post['email_code'], session_id());
        } else {
            $res = emailCodeVerify($post['new_email'], $post['email_code'], session_id());
        }
        if ($res['status'] != 1) {
            return $res;
        }

        $data = array(
            'is_email' => 1,
            'email' => $post['new_email']
        );
        $res = (new UserLogic())->saveUserInfo($data, $userId);
        return $res;
    }

    /**
     * 验证会员身份证号
     * @param array $post 会员提交的数据
     * @param int $userId 会员id
     * @return array
     */
    public function validateIdCard($post, $userId) {
        if ($post['username'] == '') {
            return array('status' => -1, 'msg' => '请输入真实姓名');
        }
        if ($post['number'] == '') {
            return array('status' => -1, 'msg' => '请输入身份证号');
        }
        if (!checkCard($post['number'])) {
            return array('status' => -1, 'msg' => '请输入正确的身份证号');
        }
        if (zfCache('securityInfo.card_img') == 1) {
            if ($post['imgz'] == '' || $post['imgf'] == '') {
                return array('status' => -1, 'msg' => '请上传身份证正反面照片');
            }
        }

        if (zfCache('securityInfo.cardAppKey') != '') {
            import('Common.Org.Curl');
            $data = array(
                'idcard' => $post['number'],
                'realname' => $post['username'],
                'key' => zfCache('securityInfo.cardAppKey')
            );
            $curl = new \Curl($data);

            $res = $curl->httpRequest('http://op.juhe.cn/idcard/query', 1);
            $res = json_decode($res, true);
            if ($res['error_code'] != 0) {
                return array('status' => -1, 'msg' => '姓名或身份证格式错误');
            }
        }


//        $data = array(
//            'is_number' => 1,
//            'username' => $post['username'],
//            'number' => $post['number'],
//            'imgz' => $post['imgz'] != '' ? $post['imgz'] : FALSE,
//            'imgf' => $post['imgf'] != '' ? $post['imgf'] : FALSE,
//        );
//        $res = (new UserLogic())->saveUserInfo($data, $userId);
//        return $res;
    }

    /**
     * 验证会员银行卡信息
     * @param array $post 会员提交的信息
     * @param int $userId 会员id
     * @return array
     */
    public function validateBank($post, $userId) {
        $user = D("UserView")->where(array('user_id' => $userId))->field('user_id,is_number,username,number')->find();
        if (!$user) {
            return array('status' => -1, 'msg' => '请先登陆');
        }
        if (zfCache('securityInfo.bankAppKey') != '') {
            if ($user['is_number'] != 1 || $user['username'] == '' || $user['number'] == '') {
                return array('status' => -1, 'msg' => '请先验证身份证');
            }
        }
      	if ($post['bank_name'] == '') {
            return array('status' => -1, 'msg' => '请输入姓名');
        }
        $post['opening_id'] = intval($post['opening_id']);
        if ($post['opening_id'] <= 0) {
            return array('status' => -1, 'msg' => '请选择开户行');
        }
        if ($post['bank_address'] == '') {
            return array('status' => -1, 'msg' => '请输入分行支行');
        }
        if ($post['bank_account'] == '') {
            return array('status' => -1, 'msg' => '请输入银行账号');
        }
        if (zfCache('securityInfo.bankAppKey') != '') {
            import('Common.Org.Curl');
            $data = array(
                'idcard' => $user['number'],
                'realname' => $post['bank_name'],
                'bankcard' => preg_replace('# #', '', $post['bank_account']),
                'key' => zfCache('securityInfo.bankAppKey')
            );
            $curl = new \Curl($data);

            $res = $curl->httpRequest('http://v.juhe.cn/verifybankcard3/query', 1);
            $res = json_decode($res, true);
            if ($res['error_code'] != 0) {
                return array('status' => -1, 'msg' => '银行账号错误');
            } elseif ($res['result']['res'] != 1) {
                return array('status' => -1, 'msg' => '银行账号与身份证不匹配');
            }
        }
        $data = array(
            'bank_name' => $post['bank_name'],
            'bank_address' => $post['bank_address'],
            'opening_id' => $post['opening_id'],
            'bank_account' => $post['bank_account'],
            'wx_name' => $post['wx_name'] != '' ? $post['wx_name'] : 0,
            'zfb_name' => $post['zfb_name'] != '' ? $post['zfb_name'] : 0,
            'ylh_name' => $post['ylh_name'] != '' ? $post['ylh_name'] : 0,
            'yft_name' => $post['yft_name'] != '' ? $post['yft_name'] : 0,
            'yhy_name' => $post['yhy_name'] != '' ? $post['yhy_name'] : 0,
        );
        $res = (new UserLogic())->saveUserInfo($data, $userId);
        return $res;
    }

}
