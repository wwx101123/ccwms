<?php

/**
 * 获取用户信息
 * @param $user_id_or_name  用户id 邮箱 手机
 * @param int $type  类型 0 id 1 手机 2 邮箱 3 账号
 * @param string $oauth  第三方来源
 * @return mixed
 */
function getUserInfo($name, $type = 0) {
    $map = array();
    if ($type == 0) {
        $map['user_id'] = $name;
    }
    if ($type == 1) {
        $map['mobile'] = $name;
    }
    if ($type == 2) {
        $map['email'] = $name;
    }
    if ($type == 3) {
        $map['account'] = $name;
    }
    $user = M('users')->where($map)->find();
    return $user;
}

/**
 * 未审会员统计
 * @param type $id
 */
function trialUserTotal($id) {
    return $res = M('users')->where(array('activate' => 2, 'level_id' => $id))->count();
}

/**
 * 未审会员统计
 * @param type $id
 */
function userActivate($id) {
    $res = M('users_activation_log')->where(array('user_id' => $id))->find();
    return $res['log_info'];
}

/**
 * 获取会员等级信息
 */
function getLevelInfo($levelId) {
    $where = array(
        'level_id' => $levelId
    );
    $levelInfo = M('users_level')->where($where)->find();

    return $levelInfo;
}

/**
 * 获取用户信息
 * @return mixed
 */
function userInfo($id) {
    return $user = M('users')->where(array('user_id' => $id))->find();
}

/**
 * 获取user_data 用户信息
 */
function dataInfo($id) {
    return $data = M('users_data')->where(array('id' => $id))->find();
}

/**
 * 获取推荐人信息
 * @return mixed
 */
function userTjrInfo($id) {
    $user = userInfo($id);
    return $tjrInfo = M('users')->where(array('user_id' => $user['tjr_id']))->cache($id)->find();
}

/**
 * 奖项名字
 * @return type
 */
function bonusInfo($id) {
    return $info = M('bonus')->where(array('bonus_id' => $id))->cache($id)->find();
}

/**
 * 税名字
 * @return type
 */
function taxInfo($id) {
    return $info = M('bonus_tax')->where(array('tax_id' => $id))->cache($id)->find();
}

/**
 * 会员收款信息
 */
function userBankInfo($id) {
    return $res = M('users_bank')->where(array('id' => $id))->find();
}

/**
 * 查询报单中心 总共有多少会员
 * @param type $id
 */
function bdrUsereTotal($id) {
    return $res = M('users')->where(array('bdr_id' => $id))->count();
}

function moneyInfo($id) {
    return $info = M('money')->where(array('money_id' => $id))->cache($id)->find();
}

function levelInfo($id) {
    return $info = M('level')->where(array('level_id' => $id))->cache($id)->find();
}

function levelTotal($id) {
    return $info = M('users')->where(array('level' => $id))->count();
}

function leaderInfo($id) {
    return $info = M('leader')->where(array('id' => $id))->find();
}

function p($data){
    // 定义样式
    $str='<pre style="display: block;padding: 9.5px;margin: 44px 0 0 0;font-size: 13px;line-height: 1.42857;color: #333;word-break: break-all;word-wrap: break-word;background-color: #F5F5F5;border: 1px solid #CCC;border-radius: 4px;">';
    // 如果是boolean或者null直接显示文字；否则print
    if (is_bool($data)) {
        $show_data=$data ? 'true' : 'false';
    }elseif (is_null($data)) {
        $show_data='null';
    }else{
        $show_data=print_r($data,true);
    }
    $str.=$show_data;
    $str.='</pre>';
    echo $str;
}
