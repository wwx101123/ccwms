<?php

namespace Zfuwl\Logic;

use Zfuwl\Model\CommonModel;

class RegLogic extends CommonModel {

    protected $tableName = 'users';


    /**
     * 添加会员
     * @param array $post 提交的结果
     * @return array
     */
    public function addUser($post)
    {
//         if ($post['level'] <= 0) {
//             return ['status' => -1, 'msg' => '请选择级别'];
//         }
//         $levelInfo = levelInfo($post['level']);
//         if(!$levelInfo) {
//             return ['status' => -1, 'msg' => '级别不存在'];
//         }

//         $count = M('users')->where(['level' => $levelInfo['level_id']])->count();
//         if($count >= $levelInfo['reg_num'] && $levelInfo['reg_num'] > 0) {
//             return ['status' => -1, 'msg' => '此等级注册已达封顶'];
//         }

        if (!checkMobile($post['account'])) {
            return ['status' => -1, 'msg' => '账号格式不正确'];
        }
        if (getUserInfo($post['account'], 3)) {
            return ['status' => -1, 'msg' => '此账号已存在, 请更换后重试'];
        }

        if (!checkPass($post['password'])) {
            return ['status' => -1, 'msg' => '登录密码格式不正确'];
        }
     //   if ($post['password'] != $post['repassword']) {
         //   return ['status' => -1, 'msg' => '登录密码两次输入不一致'];
    //    }
        if (!checkPass($post['secpwd'])) {
            return ['status' => -1, 'msg' => '二级密码格式不正确'];
        }
      //  if ($post['secpwd'] != $post['resecpwd']) {
       //     return ['status' => -1, 'msg' =>     '二级密码两次输入不一致'];
     //   }
        if ($post['password'] == $post['secpwd']) {
            return ['status' => -1, 'msg' => '登录密码和二级密码不能设置一样'];
        }
        if($post['username'] == '') {
            return ['status' => -1, 'msg' => '请输入姓名'];
        }

        $tjrId = 0;
        # 推荐人检测
        if($post['tjrAccount'] != '') {
            $tjrUser = M('users')->where(['account' => $post['tjrAccount']])->field('user_id,activate')->find();
            if(!$tjrUser) {
                return ['status' => -1, 'msg' => '此推荐人不存在'];
            } elseif ($tjrUser['activate'] != 1) {
                return ['status' => -1, 'msg' => '此推荐人未激活'];
            }
            $tjrId = $tjrUser['user_id'];
        } else {
            return ['status' => -1, 'msg' => '请输入推荐人账号'];
        }

        $userData = [
            'username' => $post['username'],
            'number' => $post['number'],
            'mobile' => $post['account']
        ];

//        $mobile && $userData['mobile'] = $mobile;
//        $email && $userData['email'] = $email;
//        $number && $userData['number'] = $number;

        $userDataId = M('users_data')->add($userData);
        $userBank = [
            'bank_name' => $post['username']
        ];
        $bankId = M('users_bank')->add($userBank);

        $userData = [
            'account' => trim($post['account'])
            ,'level' => $post['level']
            ,'tjr_id' => $tjrId
            ,'password' => webEncrypt($post['password'])
            ,'secpwd' => webEncrypt($post['secpwd'])
            ,'reg_time' => time()
            ,'tk' => zfCache('regInfo.is_tk')
            ,'frozen' => zfCache('regInfo.is_lock')
            ,'level' => 1
            ,'data_id' => $userDataId
            ,'jh_time' => zfCache('regInfo.is_acvite') == 1 ? time() : FALSE
            ,'activate' => zfCache('regInfo.is_acvite') == 1 ? 1 : 2
            ,'bank_id' => $bankId
        ];
//        $post['openid'] && $userData['openid'] = $post['openid'];

        $userId = M('users')->add($userData);

        if (!$userId) {
            return ['status' => -1, 'msg' => '注册失败'];
        } else {
//            detectUnTj($userData['tjr_id']);
//            unLockForTjr($userData['tjr_id']);
//            bochuDay('level_'.$post['level'], 1); // 统计注册会员分同级别的数量
            userBlockAdd($userId);

            userMoneyAdd($userId); // 新注册的会员统一添加钱包
            // addUserAgentSingle($userId, 1);
            return ['status' => 1, 'msg' => '注册成功', 'userId' => $userId];
        }
    }

    /**
     * 检测手机号
     * @param string $mobile 手机号
     * @return array 检测结果 可以 ['status' => 1, 'mobile' => '手机号'] 错误 ['status' => -1, 'msg' => '错误内容']
     */
    public function checkMobile($mobile)
    {
        $allowNum = intval(zfCache('regInfo.phone_num'));
        if ($allowNum > 0) {
            if($mobile == '') {
                return ['status' => -1, 'msg' => '请输入手机号'];
            }
            $num = M('users_data')->where(['mobile' => $mobile])->count();
            if($num >= $allowNum) {
                return ['status' => -1, 'msg' => '此手机号注册已达封顶'];
            }
        }
        if($mobile != '') {
            if(!checkMobile($mobile)) {
                return ['status' => -1, 'msg' => '手机号格式错误'];
            }
        }

        return ['status' => 1, 'mobile' => $mobile];
    }

    /**
     * 检测身份证号
     * @param string $card 身份证号
     * @return array 检测结果 可以 ['status' => 1, 'card' => '身份证号'] 错误 ['status' => -1, 'msg' => '错误内容']
     */
    public function checkCard($card)
    {
        $allowNum = intval(zfCache('regInfo.card_num'));
        if ($allowNum > 0) {
            if($card == '') {
                return ['status' => -1, 'msg' => '请输入身份证号'];
            }
            $num = M('users_data')->where(['number' => $card])->count();
            if($num >= $allowNum) {
                return ['status' => -1, 'msg' => '此身份证号注册已达封顶'];
            }
        }

        return ['status' => 1, 'card' => $card];
    }

    /**
     * 检测邮箱
     * @param string $email 邮箱
     * @return array 检测结果 可以 ['status' => 1, 'email' => '邮箱'] 错误 ['status' => -1, 'msg' => '错误内容']
     */
    public function checkEmail($email)
    {
        $allowNum = intval(zfCache('regInfo.email_num'));
        if ($allowNum > 0) {
            if($email == '') {
                return ['status' => -1, 'msg' => '请输入邮箱'];
            }
            $num = M('users_data')->where(['email' => $email])->count();
            if($num >= $allowNum) {
                return ['status' => -1, 'msg' => '此邮箱注册已达封顶'];
            }
        }
        if($email != '') {
            if(!checkEmail($email)) {
                return ['status' => -1, 'msg' => '邮箱格式错误'];
            }
        }

        return ['status' => 1, 'email' => $email];
    }

    /**
     * 根据帐号查询用户信息
     * @param $account 会员帐号
     * @param string $field 查询的字段
     * @param bool $isReturn 是否直接返回单个数据
     * @return mixed
     */
    public function getUserByAccount($account, $field = '', $isReturn = false) {
        $where = [
            'account' => $account
        ];
        $info = $this->where($where)->field($field)->find();
        if ($field) {
            return $info[$field];
        }
        return $info;
    }

}
