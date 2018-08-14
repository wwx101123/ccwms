<?php

function usersDay($uid, $name, $price, $money = 0, $total = 0) {
    $data['zf_time'] = strtotime(date('Y-m-d'));
    $data['uid'] = $uid;
    if (!M('users_day')->where($data)->find()) {
        M('users_day')->add($data);
    }
    $sql = "UPDATE __PREFIX__users_day SET $name = $name + $price,total = total + $total,money = money + $money  WHERE uid = $uid and zf_time = '" . strtotime(date('Y-m-d')) . "'";
    $infoId = D('users_day')->execute($sql);
    return $infoId;
}

/**
 * 日拨出比例
 */
function bochuDay($name, $price) {
    $data['zf_time'] = strtotime(date('Y-m-d')); // 今天日期
    $bochu = M('bochu_day')->where($data)->find();
    if (!$bochu) {
        M('bochu_day')->add($data);
    }
    $sqlb = "UPDATE __PREFIX__bochu_day SET $name = $name + $price  WHERE zf_time = " . strtotime(date('Y-m-d'));
    $infoId = D('bochu_day')->execute($sqlb);
    return $infoId;
}

/**
 * 拨出统计  1 日   2 周   3  月   4 季  5 年   6 总
 * @param type $type
 * @return type
 */
function clear_bochu($type, $id = 1) {
    switch ($type) {
        case 1:
            $bc = M('bochu_day')->where(array('id' => $id))->find();
            break;
    }
    $bochu = intVal($bc['zhichu'] / $bc['shuoru'] * 100);
    return $bochu;
}

function clear_bochu_user($user_id) {
    $bc = M('account_total')->where(array('user_id' => $user_id))->field('defray,money')->find();
    $bochu = intVal(($bc['money'] - $bc['defray']) / $bc['defray'] * 100);
    return $bochu;
}

/**
 * 添加会员资料表
 * @param type $useriId  会员资料值
 * @param type $moneyId   会员Id
 * @return boolean
 */
function usersDataAdd($post, $useId = '') {
    $data['username'] = $post['username'] ? $post['username'] : FALSE;
    $data['mobile'] = $post['mobile'] ? $post['mobile'] : FALSE;
    $data['email'] = $post['email'] ? $post['email'] : FALSE;
    $data['sex'] = $post['sex'] ? $post['sex'] : 3;
    $data['wx_name'] = $post['wx_name'] ? $post['wx_name'] : FALSE;
    $data['zfb_name'] = $post['zfb_name'] ? $post['zfb_name'] : FALSE;
    $data['qq_name'] = $post['qq_name'] ? $post['qq_name'] : FALSE;
    $data['wx_code'] = $post['wx_code'] ? $post['wx_code'] : FALSE;
    $data['zfb_code'] = $post['zfb_code'] ? $post['zfb_code'] : FALSE;
    $data['head'] = $post['head'] ? $post['head'] : FALSE;
    $data['number'] = $post['number'] ? $post['number'] : FALSE;
    $data['imgz'] = $post['imgz'] ? $post['imgz'] : FALSE;
    $data['imgf'] = $post['imgf'] ? $post['imgf'] : FALSE;
    $data['country'] = $post['country'] ? $post['country'] : FALSE;
    $data['province'] = $post['province'] ? $post['province'] : FALSE;
    $data['city'] = $post['city'] ? $post['city'] : FALSE;
    $data['district'] = $post['district'] ? $post['district'] : FALSE;
    $data['twon'] = $post['twon'] ? $post['twon'] : FALSE;
    $data['address'] = $post['address'] ? $post['address'] : FALSE;
    $data['is_mobile'] = $post['is_mobile'] ? $post['is_mobile'] : 2;
    $data['is_email'] = $post['is_email'] ? $post['is_email'] : 2;
    $data['is_number'] = $post['is_number'] ? $post['is_number'] : 2;
    $data['pass_name'] = $post['pass_name'] ? $post['pass_name'] : FALSE;
    $data['pass_number'] = $post['pass_number'] ? $post['pass_number'] : FALSE;
    if ($useId > 0) {
        return $res = M('users_data')->save($data);
    } else {
        return $res = M('users_data')->add($data);
    }
}

/**
 * 添加收款信息表
 * @param type $useriId  会员资料值
 * @param type $moneyId   会员Id
 * @return boolean
 */
function usersBankAdd($post, $useId = '') {
    $data['opening_id'] = $post['opening_id'] ? $post['opening_id'] : FALSE;
    $data['bank_address'] = $post['bank_address'] ? $post['bank_address'] : FALSE;
    $data['bank_account'] = $post['bank_account'] ? $post['bank_account'] : FALSE;
    $data['bank_name'] = $post['bank_name'] ? $post['bank_name'] : FALSE;
    $data['default'] = $post['default'] ? $post['default'] : 2;
    if ($useId > 0) {
        return $res = M('users_bank')->save($data);
    } else {
        return $res = M('users_bank')->add($data);
    }
}

/**
 * 添加会员表
 * @param type $useriId  会员资料值
 * @param type $moneyId   会员Id
 * @return boolean
 */
function usersAdd($post, $userId = '') {
    $data['account'] = $post['account'] ? trim($post['account']) : FALSE;
    $data['nickname'] = $post['nickname'] ? trim($post['nickname']) : FALSE;
    if ($userId > 0) {
        $data['password'] = $post['password'];
        $data['secpwd'] = $post['secpwd'];
    } else {
        $data['password'] = $post['password'] ? webEncrypt($post['repassword']) : webEncrypt(zfCache('regInfo.default_pass'));
        $data['secpwd'] = $post['secpwd'] ? webEncrypt($post['secpwd']) : webEncrypt(zfCache('regInfo.default_repass'));
    }
    $data['leader'] = $post['leader'] ? $post['leader'] : FALSE;
    $data['level'] = $post['level'] ? $post['level'] : 1;
    $data['agent'] = $post['agent'] ? $post['agent'] : FALSE;
    $data['service'] = $post['service'] ? $post['service'] : FALSE;
    $data['jh_time'] = $post['jh_time'] ? $post['jh_time'] : FALSE;
    $data['activate'] = $post['activate'] ? $post['activate'] : 2;
    $data['tjr_id'] = $post['tjr_id'] ? $post['tjr_id'] : 0;
    $data['bdr_id'] = $post['bdr_id'] ? $post['bdr_id'] : 0;
    $data['frozen'] = $post['frozen'] ? $post['frozen'] : zfCache('regInfo.is_lock');
    $data['user'] = $post['user'] ? $post['user'] : 1;
    $data['tk'] = $post['tk'] ? $post['tk'] : zfCache('regInfo.is_tk');
    $data['trends'] = $post['trends'] ? $post['trends'] : 1;
    $data['static'] = $post['static'] ? $post['static'] : 1;
    $data['data_id'] = $post['data_id'] ? $post['data_id'] : FALSE;
    $data['bank_id'] = $post['bank_id'] ? $post['bank_id'] : FALSE;
    $data['main_id'] = $post['main_id'] ? $post['main_id'] : FALSE;
    $data['reg_time'] = time();
    return $res = M('users')->add($data);
}
