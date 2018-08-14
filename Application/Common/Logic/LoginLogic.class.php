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
 * Date:2016-12-10 20:38  435
 */

namespace Common\Logic;

use Think\Model\RelationModel;

/**
 * 分类逻辑定义
 * Class CatsLogic
 * @package Home\Logic
 */
class LoginLogic extends RelationModel {

    protected $tableName = 'users';

    public function login($username, $password) {
        $result = array();
        if (!$username || !$password)
            $result = array('status' => 0, 'msg' => '请填写账号或密码');
            $user = M('users')->where("account='{$username}'")->find();
        if (!$user) {
            session('yzm', 1);
            $result = array('status' => -1, 'msg' => '账号不存在!');
        } elseif (webEncrypt($password) != $user['password']) {
            session('yzm', 1);
            userAction($user['user_id'], '密码错误', 1);
            $result = array('status' => -2, 'msg' => '密码错误!');
        } elseif ($user['frozen'] == 2) {
            userAction($user['user_id'], '账号异常已被锁定', 1);
            $result = array('status' => -3, 'msg' => '账号异常已被锁定！！！');
        } else {
            session('yzm', 0);
            userAction($user['user_id'], '登陆成功', 1);
            $result = array('status' => 1, 'msg' => '登陆成功', 'result' => $user);
        }
        return $result;
    }

}
