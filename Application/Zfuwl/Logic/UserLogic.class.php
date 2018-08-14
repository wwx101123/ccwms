<?php

namespace Zfuwl\Logic;

use Zfuwl\Model\CommonModel;

class UserLogic extends CommonModel {

    protected $tableName = 'users';

    /**
     * 修改登录密码
     * @param type $post
     */
    public function editPassword($post, $user_id = '') {
        if (!checkPass($post['confirmSecpwd'])) {
            return array('status' => -1, 'msg' => '确认密码格式不正确');
        }
        if ($post['newSecpwd'] != $post['confirmSecpwd']) {
            return array('status' => -1, 'msg' => '两次密码输入不一致');
        }
        if (strlen($post['confirmSecpwd']) < 6) {
            return array('status' => -1, 'msg' => '密码不能低于6位字符');
        }
        $num = M('password_log')->where(array('user_id' => $post['user_id'], 'is_type' => 1, 'name' => $post['confirmSecpwd']))->count();
        if ($num > zfCache('securityInfo.edit_password_num')) {
            return array('status' => -1, 'msg' => '此密码己使用过' . $num . '次，请更换');
        }
        $user = getUserInfo($post['user_id']);
        if (webEncrypt($post['confirmSecpwd']) == $user['password']) {
            return array('status' => -1, 'msg' => '新密码不能与当前密码相同');
        }
        $res = M('users')->where(array('user_id' => $post['user_id']))->save(array('password' => webEncrypt($post['confirmSecpwd'])));
        if ($res) {
            userpPasswordLog($post['user_id'], $post['confirmSecpwd'], 1);
            // 短信发送记录 暂还没有做
            // $info = M('smtp_sms_config')->where(array('status' => 2, 'is_type' => 1, 'is_class' => 1))->order('sort desc')->find();
            if ($info) {
//                $pwd = $spwd = $data['confirmSecpwd'];
//                $match = array();
//                preg_match_all('/{\$(.*?)}/', $info['content'], $match);
//                foreach ($match[1] as $key => $value) {
//                    if (isset($$value)) {
//                        $info['content'] = str_replace($match[0][$key], $$value, $info['content']);
//                    }
//                }
//                $text = "【" . zfCache('smtp_sms_info.autograph') . " 】 {$info['content']}";
//                $res = zfsendSms($data['mobile'], $text);
//                smtpSmsLog($data['userPhone'], $text, 1);
            } else {
                return array('status' => 1, 'msg' => '登录密码己修改为' . $post['confirmSecpwd']);
            }
        } else {
            return array('status' => -1, 'msg' => '新密码不能与当前密码相同');
        }
    }

    /**
     * 二级密码验证
     * @param type $post
     */
    public function validateSecpwd($post, $userId = '') {
        if (!$post['secpwd']) {
            return array('status' => -1, 'msg' => '请输入二级密码');
        }
        $user = getUserInfo($userId);
        if (webEncrypt($post['secpwd']) == $user['secpwd']) {
            return array('status' => 1, 'msg' => '验证成功');
        } else {
            return array('status' => -1, 'msg' => '二级密码验证失败');
        }
    }

    /**
     * 会员修改登录密码
     * @param array $post 密码信息
     * @param int $userId 会员id
     * @return array 操作信息
     */
    public function webEditPass($post, $userId) {
        /*if ($post['oldPass'] == '') {
            return array('status' => -1, 'msg' => '请输入当前登录密码!');
        } else*/
        if ($post['newPass'] == '') {
            return array('status' => -1, 'msg' => '请输入新登录密码!');
        } elseif (!checkPass($post['newPass'])) {
            return array('status' => -1, 'msg' => '新登录密码格式不正确!');
        } elseif ($post['confirmPass'] != $post['newPass']) {
            return array('status' => -1, 'msg' => '确认密码和新登录密码输入不一致!');
        }
        $user = getUserInfo($userId);
        /*if ($user['password'] != webEncrypt($post['oldPass'])) {
            return array('status' => -1, 'msg' => '原登录密码验证失败!');
        } else*/
        if (webEncrypt($post['newPass']) == $user['password']) {
            return array('status' => -1, 'msg' => '新登录密码不能与当前登录密码一样!');
        }
        $num = M('password_log')->where("user_id = {$user['user_id']} and is_type = 1 and name = '{$post['newPass']}'")->count();
        if ($num >= zfCache('securityInfo.edit_password_num')) {
            return array('status' => -1, 'msg' => '此密码已使用' . $num . '次，请更换!');
        }

        // 大于 0 当前的账号  就是子账号  修改所有的子账号
        if ($user['main_id'] > 0) {
            $res = M('users')->where("user_id = {$user['main_id']}")->save(array('password' => webEncrypt($post['newPass']))); // 主账号
            M('users')->where("main_id = {$user['main_id']}")->save(array('password' => webEncrypt($post['newPass']))); // 子账号
        } else {
            //  小于 等于 0  就是 主账号 修改所有的子账号
            M('users')->where("main_id = {$user['user_id']}")->save(array('password' => webEncrypt($post['newPass']))); // 子账号
            $res = M('users')->where("user_id = {$user['user_id']}")->save(array('password' => webEncrypt($post['newPass']))); // 主账号
        }
        if ($res) {
            return array('status' => 1, 'msg' => '登录密码己修改为' . $post['newPass']);
        } else {
            return array('status' => -1, 'msg' => '修改失败!');
        }
    }

    /**
     * 修改继承人信息
     */
    public function webModifyTheInheritance($post, $userId) {
        if ($post['pass_name'] == '') {
            return array('status' => -1, 'msg' => '请'
                . '输入继承人姓名');
        } elseif ($post['pass_number'] == '') {
            return array('status' => -1, 'msg' => '请输入继承人证件');
        }
        $user = userInfo($userId);
        $where = array(
            'id' => $user['data_id']
        );
        $num = M('users_data')->where($where)->save(array('pass_name' => $post['pass_name'], 'pass_number' => $post['pass_number']));
        if ($num) {
            return array('status' => 1, 'msg' => '修改成功');
        } else {
            return array('status' => -1, 'msg' => '修改失败');
        }
    }

    /**
     * 会员修改二级密码
     * @param array $post 密码信息
     * @param int $userId 会员id
     * @return array 操作信息
     */
    public function webEditSecpwd($post, $userId) {
        /*if ($post['oldPass'] == '') {
            return array('status' => -1, 'msg' => '请输入当前交易密码!');
        } else*/
        if ($post['newPass'] == '') {
            return array('status' => -1, 'msg' => '请输入新交易密码!');
        } elseif (!checkPass($post['newPass'])) {
            return array('status' => -1, 'msg' => '新交易密码格式不正确!');
        } elseif ($post['confirmPass'] != $post['newPass']) {
            return array('status' => -1, 'msg' => '确认密码和新交易密码输入不一致!');
        }
        $user = getUserInfo($userId);
        /*if ($user['secpwd'] != webEncrypt($post['oldPass'])) {
            return array('status' => -1, 'msg' => '原交易密码验证失败!');
        } else*/
        if (webEncrypt($post['newPass']) == $user['secpwd']) {
            return array('status' => -1, 'msg' => '新交易密码不能与当前交易密码一样!');
        }
        $num = M('password_log')->where("user_id = {$user['user_id']} and is_type = 2 and name = '{$post['newPass']}'")->count();
        if ($num >= zfCache('securityInfo.edit_password_num')) {
            return array('status' => -1, 'msg' => '此密码已使用' . $num . '次，请更换!');
        }
        if ($user['main_id'] > 0) {
            $res = M('users')->where("user_id = {$user['main_id']}")->save(array('secpwd' => webEncrypt($post['newPass']))); // 主账号
            M('users')->where("main_id = {$user['main_id']}")->save(array('secpwd' => webEncrypt($post['newPass']))); // 子账号
        } else {
            //  小于 等于 0  就是 主账号 修改所有的子账号
            M('users')->where("main_id = {$user['user_id']}")->save(array('secpwd' => webEncrypt($post['newPass']))); // 子账号
            $res = M('users')->where("user_id = {$user['user_id']}")->save(array('secpwd' => webEncrypt($post['newPass']))); // 主账号
        }
        if ($res) {
            return array('status' => 1, 'msg' => '交易密码己修改为' . $post['newPass']);
        } else {
            return array('status' => -1, 'msg' => '修改失败!');
        }
    }

    /**
     * 修改二级密码
     * @param type $post
     */
    public function editSecpwd($post, $user_id = '') {
        if (!checkPass($post['confirmSecpwd'])) {
            return array('status' => -1, 'msg' => '确认密码格式不正确');
        }
        if ($post['newSecpwd'] != $post['confirmSecpwd']) {
            return array('status' => -1, 'msg' => '两次密码输入不一致');
        }
        if (strlen($post['confirmSecpwd']) < 6) {
            return array('status' => -1, 'msg' => '密码不能低于6位字符');
        }
        $num = M('password_log')->where(array('user_id' => $post['user_id'], 'is_type' => 2, 'name' => $post['confirmSecpwd']))->count();
        if ($num > zfCache('securityInfo.edit_secpwd_num')) {
            return array('status' => -1, 'msg' => '此密码己使用过' . $num . '次，请更换');
        }
        $user = getUserInfo($post['user_id']);
        if (webEncrypt($post['confirmSecpwd']) == $user['secpwd']) {
            return array('status' => -1, 'msg' => '新密码不能与当前密码相同');
        }
        $res = M('users')->where(array('user_id' => $post['user_id']))->save(array('secpwd' => webEncrypt($post['confirmSecpwd'])));
        if ($res) {
            userpPasswordLog($post['user_id'], $post['confirmSecpwd'], 2);
            // 短信发送记录 暂还没有做
            // $info = M('smtp_sms_config')->where(array('status' => 2, 'is_type' => 1, 'is_class' => 1))->order('sort desc')->find();
            if ($info) {
//                $pwd = $spwd = $data['confirmSecpwd'];
//                $match = array();
//                preg_match_all('/{\$(.*?)}/', $info['content'], $match);
//                foreach ($match[1] as $key => $value) {
//                    if (isset($$value)) {
//                        $info['content'] = str_replace($match[0][$key], $$value, $info['content']);
//                    }
//                }
//                $text = "【" . zfCache('smtp_sms_info.autograph') . " 】 {$info['content']}";
//                $res = zfsendSms($data['mobile'], $text);
//                smtpSmsLog($data['userPhone'], $text, 1);
            } else {
                return array('status' => 1, 'msg' => '交易密码己修改为' . $post['confirmSecpwd']);
            }
        } else {
            return array('status' => -1, 'msg' => '新密码不能与当前密码相同');
        }
    }

    /**
     * 修改密保问题
     * @param array $post 提交的数据
     * @param int|string $user_id 会员id
     * @return array 操作结果
     */
    public function editSecurity($post, $uId) {
        if ($uId <= 0) {
            return array('status' => -1, 'msg' => '请刷新生重试');
        }
        if ($post['security_ida'] == $post['security_idb']) {
            return array('status' => -1, 'msg' => '密保选择不能一样');
        }
        if ($post['security_ida'] == $post['security_idc']) {
            return array('status' => -1, 'msg' => '密保选择不能一样');
        }
        if ($post['security_idb'] == $post['security_idc']) {
            return array('status' => -1, 'msg' => '密保选择不能一样');
        }
        $data['p_one'] = $post['security_ida'];
        $data['d_one'] = $post['answera'];
        $data['p_two'] = $post['security_idb'];
        $data['d_two'] = $post['answerb'];
        $data['p_three'] = $post['security_idc'];
        $data['d_three'] = $post['answerc'];
        if ($post['id']) {
            $data['edit_time'] = time();
            $res = M('users_security')->where(array('id' => $post['id'], 'uid' => $uId))->save($data);
        } else {
            $data['add_time'] = time();
            $data['uid'] = $uId;
            $res = M('users_security')->add($data);
        }
        if ($res) {
            return array('status' => 1, 'msg' => '密保己修改成功');
        } else {
            return array('status' => -1, 'msg' => '密保修改失败');
        }
    }

    /**
     * 修改收款信息
     * @param type $post
     */
    public function editBank($post, $uId) {
        if ($post['opening_id'] <= 0) {
            return array('status' => -1, 'msg' => '请选择开户银行');
        }
        if ($post['bank_account'] != '') {
            if (!checkBankCard($post['bank_account'])) {
                return array('status' => -1, 'msg' => '银行卡号不正确');
            }
        }
        $res = $this->saveUserInfo($post, $uId);
        return $res;
        if (!$resId) {
            return array('status' => -1, 'msg' => '操作失败');
        } else {
            return array('status' => 1, 'msg' => '操作成功');
        }
    }

    /**
     * 修改手机号码
     * @param type $post
     */
    public function editMobile($post, $user_id = '') {
        $user = getUserInfo($post['user_id']);
        if (!$user) {
            return array('status' => -1, 'msg' => '会员账号不存在');
        } else {
            if ($post['mobile'] == $post['new_mobile']) {
                return array('status' => -1, 'msg' => '新手机号不能与原手机号相同');
            }
            $data['mobile'] = $post['new_mobile'];
            $resId = M('users')->where(array('user_id' => $post['user_id']))->save($data);
            if (!$resId) {
                return array('status' => -1, 'msg' => '操作失败');
            } else {
                return array('status' => 1, 'msg' => '操作成功');
            }
        }
    }

    /**
     * 修改会员信息
     */
    public function saveUserInfo($post, $userId) {
        if (intval($userId) <= 0) {
            return array('status' => -1, 'msg' => '请刷新页面后重试');
        }
        $userInfo = getUserInfo($userId);

        $userArr = $userDataArr = $userBankArr = array();
        $dataRes = $bankRes = true;

        # 会员资料
        $post['nickname'] && $userArr['nickname'] = $post['nickname'];
        $post['service'] && $userArr['service'] = $post['service'];
        $post['jiangjin_jihuo_status'] && $userArr['jiangjin_jihuo_status'] = $post['jiangjin_jihuo_status'];
        # 会员详细信息
        $post['wx_name'] && $userDataArr['wx_name'] = $post['wx_name'];
        $post['wx_code'] && $userDataArr['wx_code'] = $post['wx_code'];
        $post['zfb_name'] && $userDataArr['zfb_name'] = $post['zfb_name'];
        $post['zfb_code'] && $userDataArr['zfb_code'] = $post['zfb_code'];
        $post['bank_name'] && $userDataArr['username'] = $post['bank_name'];
        $post['mobile'] && $userDataArr['mobile'] = $post['mobile'];
        $post['email'] && $userDataArr['email'] = $post['email'];
        $post['head'] && $userDataArr['head'] = $post['head'];
        $post['province'] && $userDataArr['province'] = $post['province'];
        $post['city'] && $userDataArr['city'] = $post['city'];
        $post['district'] && $userDataArr['district'] = $post['district'];
        $post['twon'] && $userDataArr['twon'] = $post['twon'];
        $post['address'] && $userDataArr['address'] = $post['address'];
        $post['is_mobile'] && $userDataArr['is_mobile'] = $post['is_mobile'];
        $post['is_email'] && $userDataArr['is_email'] = $post['is_email'];
        $post['is_number'] && $userDataArr['is_number'] = $post['is_number'];
        $post['pass_name'] && $userDataArr['pass_name'] = $post['pass_name'];
        $post['pass_number'] && $userDataArr['pass_number'] = $post['pass_number'];
        $post['qq_name'] && $userDataArr['qq_name'] = $post['qq_name'];
        $post['number'] && $userDataArr['number'] = $post['number'];
        $post['imgz'] && $userDataArr['imgz'] = $post['imgz'];
        $post['imgf'] && $userDataArr['imgf'] = $post['imgf'];
        $post['ylh_name'] && $userDataArr['ylh_name'] = $post['ylh_name'];
        $post['yft_name'] && $userDataArr['yft_name'] = $post['yft_name'];
        $post['yhy_name'] && $userDataArr['yhy_name'] = $post['yhy_name'];
        $post['dl_province'] && $userDataArr['dl_province'] = $post['dl_province'];
        $post['dl_city'] && $userDataArr['dl_city'] = $post['dl_city'];
        $post['dl_district'] && $userDataArr['dl_district'] = $post['dl_district'];
        # 提现信息
        $post['opening_id'] && $userBankArr['opening_id'] = $post['opening_id'];
        $post['bank_address'] && $userBankArr['bank_address'] = $post['bank_address'];
        $post['bank_account'] && $userBankArr['bank_account'] = $post['bank_account'];
        $post['bank_name'] && $userBankArr['bank_name'] = $post['bank_name'];
        if ($userDataArr) {
            $userDataInfo = dataInfo($userInfo['data_id']);
            if ($userDataInfo) {
                $dataRes = M('users_data')->where(array('id' => $userInfo['data_id']))->save($userDataArr);
            } else {
                $dataRes = $dataId = M('users_data')->add($userDataArr);
                $userArr['data_id'] = $dataId;
            }
        }
        if ($userBankArr) {
            $userBankInfo = userBankInfo($userInfo['bank_id']);
            if ($userBankInfo) {
                $bankRes = M('users_bank')->where(array('id' => $userInfo['bank_id']))->save($userBankArr);
            } else {
                $userBankArr['uid'] = $userId;
                $bankRes = $bankId = M('users_bank')->add($userBankArr);
                $userArr['bank_id'] = $bankId;
            }
        }
        if ($userArr) {
            M('users')->where(array('user_id' => $userId))->save($userArr);
        }

        if ($bankRes || $dataRes) {
            return array('status' => 1, 'msg' => '操作成功');
        } else {
            return array('status' => -1, 'msg' => '操作失败');
        }
    }

    /**
     * 修改基本信息
     * @param type $post
     */
    public function editData($post, $userId) {
        if (intval($userId) <= 0) {
            return array('status' => -1, 'msg' => '请刷新页面后重试');
        }
        if ($post['mobile'] != '') {
            if (!checkMobile($post['mobile'])) {
                return array('status' => -1, 'msg' => '手机号格式不正确');
            }
        }
        if ($post['email'] != '') {
            if (!checkEmail($post['email'])) {
                return array('status' => -1, 'msg' => '邮箱格式不正确');
            }
        }
        if ($post['number'] != '') {
            if (!checkCard($post['number'])) {
                return array('status' => -1, 'msg' => '身份证号码不正确');
            }
        }
        $res = $this->saveUserInfo($post, $userId);
        return $res;
        if (!$res) {
            return array('status' => -1, 'msg' => '添加失败');
        } else {
            return array('status' => 1, 'msg' => '添加成功');
        }
    }

    /**
     * 冻结会员
     * @param [array] $post    [post数据]
     * @param [int] $user_id [会员]
     */
    public function addFrozen($post) {
        $user = M("users")->where(array('user_id' => $post['id']))->field('user_id,account,frozen')->find();
        if ($user['user_id'] <= 0) {
            return array('status' => -1, 'msg' => '会员账号不存在!');
        }
        if ($user['frozen'] != 1) {
            return array('status' => -1, 'msg' => '请不要重复冻结!');
        }
        if ($post['name'] == '') {
            return array('status' => -1, 'msg' => '请输入冻结原因!');
        }
        $logId = userLockLog($user['user_id'], $post['name'], session('admin_id'));
        $acId = userAction($user['user_id'], $post['name'] . '冻结');
        if ($logId && $acId) {
            adminLogAdd($post['name'] . '冻结会员' . $user['account']);
            return array('status' => 1, 'msg' => '冻结成功!');
        } else {
            return array('status' => -1, 'msg' => '冻结失败!');
        }
    }

    /**
     * 解除会员冻结状态
     * @param type $post
     * @param type $user_id
     * @return type
     */
    public function releaseFrozen($post) {
        if ($post['note'] == '') {
            return array('status' => -1, 'msg' => '备注不能为空');
        }
        if ($post['lock_id'] == '') {
            return array('status' => -1, 'msg' => '请刷新后重试');
        }
        if ($post['user_id'] <= 0) {
            return array('status' => -1, 'msg' => '请刷新后重试');
        } else {
            $user = M('users')->where(array('user_id' => $post['user_id']))->field('user_id,account,frozen')->find();
            if ($user['frozen'] == 1) {
                return array('status' => -1, 'msg' => '请不要重复解除');
            }
            $lockId = M('users_lock')->where(array('id' => $post['lock_id']))->save(array('statu' => 1, 'edit_time' => time(), 'log_note' => $post['note'], 'edit_admin_id' => session('admin_id')));
            if ($lockId) {
                $userId = M('users')->where(array('user_id' => $user['user_id']))->save(array('frozen' => 1));
                if ($userId) {
                    adminLogAdd($post['note'] . '释放会员' . $user['account']);
                    userAction($user['user_id'], $post['note'] . '释放');
                    return array('status' => 1, 'msg' => '操作成功');
                } else {
                    return array('status' => -1, 'msg' => '操作失败');
                }
            }
        }
    }

    /**
     * 回填单会员转实单会员
     * @param type $post
     * @param type $user_id
     * @return type
     */
    public function editIsUserReal($post, $user_id = '') {
        $user = getUserInfo($post['user_id']);
        if (!$user) {
            return array('status' => -1, 'msg' => '会员账号不存在');
        } else {
            if ($user['is_user'] != 3) {
                return array('status' => -1, 'msg' => '此会员非回填单');
            }
            if ($post['note'] == '') {
                return array('status' => -1, 'msg' => '备注不能为空');
            }
            $lockId = userAction($user['user_id'], $post['note'] . session('admin_name') . '回填单转实单');
            if ($lockId) {
                $userId = M('users')->where(array('user_id' => $user['user_id']))->save(array('is_user' => 1));
                if (!$userId) {
                    return array('status' => -1, 'msg' => '操作失败');
                } else {
                    return array('status' => 1, 'msg' => '操作成功');
                }
            }
        }
    }

    /**
     * 空单会员转实单会员
     * @param type $post
     * @param type $user_id
     * @return type
     */
    public function editIsUserEmpty($post, $user_id = '') {
        $user = getUserInfo($post['user_id']);
        if (!$user) {
            return array('status' => -1, 'msg' => '会员账号不存在');
        } else {
            if ($user['is_user'] != 2) {
                return array('status' => -1, 'msg' => '此会员非空单');
            }
            if ($post['note'] == '') {
                return array('status' => -1, 'msg' => '备注不能为空');
            }
            $lockId = userAction($user['user_id'], $post['note'] . session('admin_name') . '空单转实单');
            if ($lockId) {
                $userId = M('users')->where(array('user_id' => $user['user_id']))->save(array('is_user' => 1));
                $level = M('users_level')->where(array('level_id' => $user['level_id']))->find();
                if ($level['giveb_price'] > 0 && $level['giveb_mid'] > 0) {
                    $givebId = userMoneyAddLog($user['user_id'], $level['giveb_mid'], $level['giveb_price'], 0, 107, '空单转实单赠送');
                    activeteSharesAdd($user['user_id'], $level['giveb_price']);
                }
                $sjMoney = $level['amount'] * zfCache('securityInfo.tj_yj_num') / 100;
                bonus2ClearThe($user['user_id'], $sjMoney, $user['account'] . '空单转实单'); // 统 计双轨业绩
                bonus1TjrClearSj($user['tjr_id'], $sjMoney, $user['account'] . '空单转实单'); // 推荐奖
                bonus2ClearSj($user['account'] . '空单转实单'); // 量碰奖
                bonus4ClearSj($user['user_id'], $sjMoney, $user['account'] . '空单转实单'); // 见点奖
                adminLogAdd($post['note'] . '空单转实单');
                if (!$userId) {
                    return array('status' => -1, 'msg' => '操作失败');
                } else {
                    return array('status' => 1, 'msg' => '操作成功');
                }
            }
        }
    }

    /**
     * 根据帐号查询用户信息
     * @param $account 会员帐号
     * @param string $field 查询的字段
     * @param bool $isReturn 是否直接返回单个数据
     * @return mixed
     */
    public function getUserByAccount($account, $field = '', $isReturn = false) {
        $where = array('account' => $account);
        $info = $this->where($where)->field($field)->find();
        if ($field) {
            return $info[$field];
        }
        return $info;
    }

    /**
     * 通过手机验证码找回密码
     * @param type $post
     * @param type $user_id
     * @return type
     */
    public function mobilePassword($post, $user_id = '') {
        $user = D("UserView")->where(array('mobile' => $post['account']))->find();
        if (!$user) {
            return array('status' => -1, 'msg' => '手机号不存在');
        } else {
            if ($post['new_pass'] != $post['confirm_new_pass']) {
                return array('status' => -1, 'msg' => '两次密码输入不一致');
            }
            $res = M('users')->where(array('user_id' => $user['user_id']))->save(array('password' => webEncrypt($post['new_pass'])));
            if ($res) {
//                $info = M('smtp_sms_config')->where(array('status' => 2, 'is_type' => 1, 'is_class' => 1))->order('sort desc')->find();
//                if ($info) {
//                    $pwd = $spwd = $data['confirmSecpwd'];
//                    $match = array();
//                    preg_match_all('/{\$(.*?)}/', $info['content'], $match);
//                    foreach ($match[1] as $key => $value) {
//                        if (isset($$value)) {
//                            $info['content'] = str_replace($match[0][$key], $$value, $info['content']);
//                        }
//                    }
//                    $text = "【" . zfCache('smtp_sms_info.autograph') . " 】 {$info['content']}";
//                    $res = zfsendSms($data['mobile'], $text);
//                    smtpSmsLog($data['userPhone'], $text, 1);
//                }
                return array('status' => 1, 'msg' => ',新登录密码为' . $post['new_pass']);
            } else {
                return array('status' => -1, 'msg' => '修改后的密码与原密码有可能');
            }
        }
    }

    /**
     * 通过密保问题 找回密码
     * @param type $post
     * @param type $user_id
     */
    public function securityPassword($post, $user_id = '') {
        $user = getUserInfo($post['account'], 3);
        if (!$user) {
            return array('status' => -1, 'msg' => '请输入正确的会员账号');
        } else {
            if ($post['newSecpwd'] != $post['confirmSecpwd']) {
                return array('status' => -1, 'msg' => '两次密码输入不一致');
            }
            if ($post['security_id'] != $user['security_id']) {
                return array('status' => -1, 'msg' => '密保问题验证失败');
            }
            if ($post['answer'] != $user['answer']) {
                return array('status' => -1, 'msg' => '密保答案验证失败');
            }
            $res = M('users')->where(array('user_id' => $user['user_id']))->save(array('password' => webEncrypt($post['confirmSecpwd'])));
            if ($res) {
                return array('status' => 1, 'msg' => ',新登录密码为' . $post['confirmSecpwd']);
            } else {
                return array('status' => -1, 'msg' => '修改后的密码与原密码有可能');
            }
        }
    }

    public function emailPassword($post, $user_id = '') {
        $user = getUserInfo($post['account'], 3);
        if (!$user) {
            return array('status' => -1, 'msg' => '请输入正确的会员账号');
        } else {
            if ($post['email'] == $user['email']) {
                $code = getRanduNum();
                $content = '你的登录密码己成功重置为：' . $code;
                if (checkEmail($post['email'])) {
                    $data = array(
                        'name' => $post['email'],
                        'zf_time' => time(),
                        'content' => $content,
                        'is_type' => 2,
                        'is_class' => 2,
                        'session_id' => session_id()
                    );
                    $smsRes = M('sms_log')->add($data);
                    $res = sendMail($post['email'], $user['account'], '找回密码', $content);
                    if ($res) {
                        M('sms_log')->where("id = {$smsRes}")->save(array('is_type' => 1));
                    }
                }
                M('users')->where(array('user_id' => $user['user_id']))->save(array('password' => webEncrypt($code)));
                return array('status' => 1, 'msg' => '密码重置成功，请查收邮件');
            } else {
                return array('status' => -1, 'msg' => '邮箱账号验证失败');
            }
        }
    }

    /**
     * 删除未激活的会员
     * @param array or int $userId 要删除的会员的id
     * @return array 删除信息
     */
    public function delWeUsers($userId) {
        if ($userId > 0) {
            $user = M('users')->where(array('user_id' => $userId))->find();
            if (!$user) {
                return array('status' => -1, 'msg' => '此会员不存在');
            }
            $tjNum = M('users')->where(array('tjr_id' => $userId))->count();
            if ($tjNum > 0) {
                return array('status' => -1, 'msg' => '还存在' . $tjNum . '名会员未修改推荐人，请修改后再操作');
            }
            $bdNum = M('users')->where(array('bdr_id' => $userId))->count();
            if ($bdNum > 0) {
                return array('status' => -1, 'msg' => '还存在' . $bdNum . '名会员未修改报单人，请修改后再操作');
            }
            $userNum = M('users')->where(array('data_id' => $user['data_id']))->count();
            $data = M('users_data')->where(array('id' => $user['data_id']))->find();
            if ($userNum <= 1 && $data['id'] > 0) {
                M('users_data')->where(array('id' => $user['data_id']))->delete();
            }
            $res = M('users')->where(array('user_id' => $user['user_id']))->delete();
            if ($res) {
                return array('status' => 1, 'msg' => '删除成功!');
            } else {
                return array('status' => -1, 'msg' => '删除失败!');
            }
        } else {
            return array('status' => -1, 'msg' => '刷新生重试!');
        }
    }
	
  	/**
     * 删除会员
     * @param array or int $userId 要删除的会员的id
     * @return array 删除信息
     */
    public function delUsers($userId) {
        if ($userId > 0) {
            $user = M('users')->where(array('user_id' => $userId))->find();
            if (!$user) {
                return array('status' => -1, 'msg' => '此会员不存在');
            }
            $tjNum = M('users')->where(array('tjr_id' => $userId))->count();
            if ($tjNum > 0) {
                return array('status' => -1, 'msg' => '还存在' . $tjNum . '名会员未修改推荐人，请修改后再操作');
            }

            $userNum = M('users')->where(array('data_id' => $user['data_id']))->count();
            $data = M('users_data')->where(array('id' => $user['data_id']))->find();
            if ($userNum <= 1 && $data['id'] > 0) {
                M('users_data')->where(array('id' => $user['data_id']))->delete();
            }
            $res = M('users')->where(array('user_id' => $user['user_id']))->delete();
            if ($res) {
                M('users_money')->where(['uid' => $userId])->delete();
                M('block_user')->where(['uid' => $userId])->delete();
                return array('status' => 1, 'msg' => '删除成功!');
            } else {
                return array('status' => -1, 'msg' => '删除失败!');
            }
        } else {
            return array('status' => -1, 'msg' => '刷新后重试!');
        }
    }
  
    /**
     * 修改会员信誉
     */
    public function editXinyu($post) {
        if ($post['name'] <= 0) {
            return array('status' => -1, 'msg' => '请输入信誉级别');
        } elseif ($post['name'] > zfCache('securityInfo.xinyu')) {
            return array('status' => -1, 'msg' => '最高不能超过黙认值' . zfCache('securityInfo.xinyu'));
        }
        if ($post['id'] > 0) {
            $user = userInfo($post['id']);
            $where = array(
                'user_id' => $user['user_id']
            );
            $num = M('users')->where($where)->save(array('xinyu' => $post['name']));
            if ($num) {
                adminLogAdd('修改会员信誉' . $user['xinyu'] . '星为' . $post['name'] . '星');
                return array('status' => 1, 'msg' => '修改成功');
            } else {
                return array('status' => -1, 'msg' => '修改失败');
            }
        } else {
            return array('status' => -1, 'msg' => '请刷新后重试');
        }
    }

    /**
     * 修改推荐人账号
     */
    public function editUserTjr($post) {
        if ($post['name'] == '') {
            return array('status' => -1, 'msg' => '请输入新推荐人账号');
        }
        if ($post['id'] <= 0) {
            return array('status' => -1, 'msg' => '请刷新页面后重试');
        }
        $editUser = M('users')->where(array('user_id' => trim($post['id'])))->field('user_id,account,tjr_id')->find();
        $tjrUser = M('users')->where(array('user_id' => trim($editUser['tjr_id'])))->field('account')->find();
        $user = M('users')->where(array('account' => trim($post['name'])))->field('user_id,account')->find();
        if ($user['user_id'] > 0) {
            if ($user['user_id'] == $editUser['user_id']) {
                return array('status' => -1, 'msg' => '自己不能是自己的推荐人');
            }
            if ($user['user_id'] == $editUser['tjr_id']) {
                return array('status' => -1, 'msg' => '修改前与修改后不能同一账号');
            }
            if ($user['user_id'] > $editUser['user_id']) {
                return array('status' => -1, 'msg' => '推荐人不能是下级会员');
            }
            $uId = M('users')->where(array('user_id' => $post['id']))->save(array('tjr_id' => $user['user_id']));
            if ($uId) {
                adminLogAdd('修改' . $editUser['account'] . '推荐人' . $tjrUser['account'] . '为' . $user['account']);
                userAction($editUser['user_id'], '推荐人由' . $tjrUser['account'] . '变更为' . $user['account']);
                return array('status' => 1, 'msg' => '修改成功');
            } else {
                return array('status' => -1, 'msg' => '修改失败');
            }
        } else {
            return array('status' => -1, 'msg' => $post['name_cn'] . '账号不存在');
        }
    }

    /**
     * 修改报单人账号
     */
    public function editUserBdr($post) {
        if ($post['name'] == '') {
            return array('status' => -1, 'msg' => '请输入新报单人账号');
        }
        if ($post['id'] <= 0) {
            return array('status' => -1, 'msg' => '请刷新页面后重试');
        }
        $editUser = M('users')->where(array('user_id' => trim($post['id'])))->field('user_id,account,bdr_id')->find();
        $bdrUser = M('users')->where(array('user_id' => trim($editUser['bdr_id'])))->field('account')->find();
        $user = M('users')->where(array('account' => trim($post['name'])))->field('user_id,account')->find();
        if ($user['user_id'] > 0) {
            if ($user['user_id'] == $editUser['user_id']) {
                return array('status' => -1, 'msg' => '自己不能是自己的报单人');
            }
            if ($user['user_id'] == $editUser['tjr_id']) {
                return array('status' => -1, 'msg' => '修改前与修改后不能同一账号');
            }
            if ($user['user_id'] > $editUser['user_id']) {
                return array('status' => -1, 'msg' => '报单人不能是下级会员');
            }
            $uId = M('users')->where(array('user_id' => $post['id']))->save(array('bdr_id' => $user['user_id']));
            if ($uId) {
                adminLogAdd('修改' . $editUser['account'] . '报单人' . $bdrUser['account'] . '为' . $user['account']);
                userAction($editUser['user_id'], '报单人由' . $bdrUser['account'] . '变更为' . $user['account']);
                return array('status' => 1, 'msg' => '修改成功');
            } else {
                return array('status' => -1, 'msg' => '修改失败');
            }
        } else {
            return array('status' => -1, 'msg' => $post['name_cn'] . '账号不存在');
        }
    }

    /**
     * 升级
     * @param array $post 提交的数据
     * @param int $userId 会员id
     * @return array 返回结果
     */
    public function upgrade($post, $userId) {
        $user = M('users')->where(array('user_id' => $userId))->field('user_id,level,secpwd')->find();
        if (!$user) {
            return array('status' => -1, 'msg' => '请先登录后再操作');
        }
        $count = M('users_play_money')->where(array('uid' => $userId, 'status' => 1))->count();
        if ($count > 0) {
            return array('status' => -1, 'msg' => '你上次提交的对方还未确认，暂时不能提交');
        }
        $post['level_id'] = intval($post['level_id']);
        if ($post['level_id'] <= 0) {
            return array('status' => -1, 'msg' => '请选择等级');
        }
        if ($post['level_id'] == $user['level']) {
            return array('status' => -1, 'msg' => '新等级不能与当前等级一样');
        }

        $level = M('level')->where(array('level_id' => $post['level_id']))->field('amount, tjr_num, level_id')->find();
        if (!$level) {
            return array('status' => -1, 'msg' => '该会员等级不存在');
        }
        if (webEncrypt($post['secpwd']) != $user['secpwd']) {
            return array('status' => -1, 'msg' => '交易密码验证失败');
        }

        # 接点会员
        $jdrId = getJdrPrevId($userId, (($level['level_id'] - 1) <= 0 ? 1 : ($level['level_id'] - 1)));
        if ($jdrId == $userId) {
            return array('status' => -1, 'msg' => '你不能升级');
        }
        # 接点人信息
        $jdrUser = M('users')->where(array('user_id' => $jdrId))->field('level,power')->find();
        # 接点人直推人数
        if ($jdrUser['power'] != 1 && !in_array($level['level_id'], array(1,2,3))) {
            # 20180317 添加的，管理员在后台 设置 不接受考核条件的收款
            $jdrTjrNum = M('users')->where(array('tjr_id' => $jdrId))->count();
            if (!$jdrUser || $jdrUser['level'] < $post['level_id'] || $jdrTjrNum < $level['tjr_num']) {
                return array('status' => -1, 'msg' => '接点人不满足条件');
            }
        }


        $jdrSkNum = M('users_play_money')->where(array('to_uid' => $jdrId, 'status' => 1))->count();
        if ($jdrSkNum > 0) {
            return array('status' => -1, 'msg' => '对方还有订单未确认暂时不能提交');
        }


        $data = array(
            'uid' => $userId,
            'to_uid' => $jdrId,
            'add_time' => time(),
            'status' => 1,
            'note' => $post['note'],
            'money' => $level['amount'],
            'level_id' => $post['level_id']
        );
        // if($post['level_id'] == 1) {
        $data['is_hk'] = 1;
        $data['hk_status'] = 2;
        // }

        $res = M('users_play_money')->add($data);
        if ($res) {
            return array('status' => 1, 'msg' => '提交成功，等待对方确认');
        } else {
            return array('status' => -1, 'msg' => '提交失败');
        }
    }
    /**
     * 接点人位置转换
     */
    public function jdrPosSwitch($post)
    {

        if($post['account'] == '')
        {
            return array('status' => -1, 'msg' => '请输入会员账号');
        }
        if($post['jdrAccount'] == '')
        {
            return array('status' => -1, 'msg' => '请输入接点人账号');
        }
        if($post['pos'] == '')
        {
            return array('status' => -1, 'msg' => '请选择接点位置');
        }

        # 获取会员信息
        $user = M('users')->where(array('account' => $post['account']))->find();
        if(!$user)
        {
            return array('status' => -1, 'msg' => '会员不存在');
        }
        # 获取接点人信息
        $jdrUser = M('users')->where(array('account' => $post['jdrAccount']))->find();
        if(!$jdrUser)
        {
            return array('status' => -1, 'msg' => '接点人不存在');
        }

        $jdrUserBranch = M('users_branch')->where(['uid' => $jdrUser['user_id']])->find();

        $jdrUserBranch = M('users_branch')->where(['uid' => $jdrUser['user_id']])->find();
        if($jdrUserBranch['br_num'] < $post['pos']) {
            return ['status' => -1, 'msg' => '此接点人还未开放该区'];
        }


        $count = M('users_branch')->where(['jdr_id' => $jdrUserBranch['branch_id'], 'position' => $post['pos']])->count();
        if($count > 0)
        {
            return array('status' => -1, 'msg' => '该位置已被注册');
        }

        $userBranch = M('users_branch')->where(array('uid' => $user['user_id']))->find();

        $userJdrBranYj = M('users_branch_yj')->where(['b_id' => $userBranch['jdr_id'], 'type' => $userBranch['position']])->find();
        $this->regulationYj($userBranch['jdr_id'], $userBranch['position'], floatval('-'.$userJdrBranYj['new']), floatval('-'.$userJdrBranYj['total']));
        
        $jdrBranch = M('users_branch')->where(array('uid' => $jdrUser['user_id']))->find();
        $data = array(
            'path' => $jdrBranch['path'].','.$jdrBranch['branch_id'],
            'path_num' => $jdrBranch['path_num']+1,
            'sy_num' => $jdrBranch['sy_num']*3-(3-($post['pos'] + 1)),
            'position' => $post['pos'],
            'jdr_id' => $jdrBranch['branch_id']
        );
        M('users_branch')->where(array('uid' => $userBranch['uid']))->save($data);
        $this->regulationYj($jdrBranch['branch_id'], $post['pos'], floatval($userJdrBranYj['new']), floatval($userJdrBranYj['total']));

        $userList = M('users_branch')->where(array('path' => array('like', '%'.$userBranch['path'].','.$userBranch['branch_id'].'%')))->order('length(path) asc')->select();
        foreach($userList as $v)
        {
            $branch = M('users_branch')->where(array('branch_id' => $v['jdr_id']))->find();
            $data = array(
                'path' => $branch['path'].','.$branch['branch_id'],
                'path_num' => $branch['path_num']+1,
                'sy_num' => $branch['sy_num']*3-(3-($v['position'] + 1))
            );
            M('users_branch')->where(array('uid' => $v['uid']))->save($data);
        }


        return array('status' => 1, 'msg' => '操作成功');
    }

    /**
     * 增减业绩
     * @param int $jdrId 接点人id
     * @param int $pos 接点位置
     * @param float $new 剩余业绩
     * @param float $total 总业绩
     * @return bool
     */
    public function regulationYj($jdrId, $pos, $new, $total)
    {
        if($new != 0 || $total != 0) {
            $branchInfo = M('users_branch')->where(['branch_id' => $jdrId])->field('jdr_id, uid, branch_id, position')->find();

            $branchYjInfo = M('users_branch_yj')->where(['uid' => $branchInfo['uid'], 'b_id' => $branchInfo['branch_id'], 'type' => $pos])->find();

            $data = [
                'total' => $branchYjInfo['total']+$total
                ,'new' => $branchYjInfo['new']+$new
            ];

            M("users_branch_yj")->where(['yj_id' => $branchYjInfo['yj_id']])->save($data);
            if($branchInfo['jdr_id'] > 0) {
                $this->regulationYj($branchInfo['jdr_id'], $branchInfo['position'], $new, $total);
            }
        }

        return true;
    }

  
  	/**
     * 管理员手动修改会员的投资金额
     * @param array $post
     * @param type $uid
     * @return type
     */
    public function editInvestMoney($post) {
        $post['money'] = (float) $post['name'];
        
        $user = M('users')->where(['user_id' => $post['id']])->find();
        if (M('users')->where(['user_id' => $post['id']])->save(['invest_money' => $post['money']])) {
            adminLogAdd('调整' . $user['account'] . '投资金额' . $user['invest_money'] . '为' . $post['money']);
          	if ($user['tjr_id'] > 0) {
                bonus1Clear($user['tjr_id'], $post['id'], $post['money'], '投资总金额' . $post['money']);
            }
            return array('status' => 1, 'msg' => '操作成功!');
        } else {
            return array('status' => -1, 'msg' => '操作失败!');
        }
    }

}
