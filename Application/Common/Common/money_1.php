<?php

/**
 * 新注册的会员 添加钱包
 * @param type $userId
 */
function userMoneyAdd($userId) {
    $list = M('money')->field('money_id')->select();
    if ($list) {
        foreach ($list as $v) {
            $data['uid'] = $userId;
            $data['mid'] = $v['money_id'];
            M('users_money')->add($data);
        }
    }
    return TRUE;
}

/**
 * 新添加钱包的时候 所有的会员 都添加一个钱包字段
 * @param type $userId
 */
function moneyUserAdd($mId) {
    $list = M('users')->field('user_id')->select();
    if ($list) {
        foreach ($list as $v) {
            $data['uid'] = $v['user_id'];
            $data['mid'] = $mId;
            M('users_money')->add($data);
        }
    }
    return TRUE;
}

/**
 * 钱包管理
 * @param type $mId  钱包 id
 * @return type
 */
function moneyOne($mId) {
    return M('money')->where(array('money_id' => $mId))->cache('moneyOne' . $mId)->find();
}

/**
 * 查询会员可用余额
 * @param int $uId 会员 ID
 * @param int $mId 钱包 ID
 * @param int $a   1 查询可用余额   2  查询冻结金额 100所有钱包总和
 * @return float|int 金额
 */
function usersMoney($uId, $mId, $a = 1) {
    if ($a == 100) {
        return floatval(M('users_money')->where(array('uid' => $uId))->sum('money'));
    }
    $info = M('users_money')->where(array('uid' => $uId, 'mid' => $mId))->find();
    if ($info) {
        if ($a == 1) {
            return $info['money'];
        }
        if ($a == 2) {
            return $info['frozen'];
        }
    } else {
        return 0;
    }
}

/**
 * 查询会员可用余额 gp001
 * @param int $user_id 会员Id
 * @param int $money_id 钱包id
 * @param int $a 类型 1查询余额 2查询冻结金额 3查询总额
 * @return int
 */
function usersMoneyForNext($user_id, $money_id, $a = 1) {
    $info = M('users_money')->where(array('user_id' => $user_id, 'money_id' => $money_id))->find();
    if ($info) {
        if ($a == 1) {
            return $info['money'];
        }
        if ($a == 2) {
            return $info['frozen'];
        }
        if ($a == 3) {
            return $info['total'];
        }
    }
    return 0;
}

/**
 * 会员钱包变动日志 添加
 * @param type $uId 会员 ID
 * @param type $mId 钱包 ID
 * @param type $money 变动金额
 * @param type $type 变动类型
 * @param type $note 备注
 * @param type $adminId 管理员 ID
 * @param type $comeUid  来源于那个会员 ID
 * @return type
 */
function userMoneyLogAdd($uId, $mId, $money = 0, $type, $note = '', $adminId = '', $comeUid = '') {
    $acc['uid'] = $uId;
    $acc['mid'] = $mId;
    $acc['money'] = $money;
    $acc['is_type'] = $type;
    $acc['zf_time'] = time();
    $note && $acc['note'] = $note;
    $adminId && $acc['admin_id'] = $adminId;
    $comeUid && $acc['come_uid'] = $comeUid;
    $sql = "UPDATE __PREFIX__users_money SET money = money + $money  WHERE uid = $uId and mid = $mId";
    if (D('users_money')->execute($sql)) {
        $acc['total'] = M('users_money')->where(array('uid' => $uId, 'mid' => $mId))->getField('money');
        return M('money_log')->add($acc);
    } else {
        return false;
    }
}

/**
 *  资金变动明细 gp001
 * @param int $user_id 会员ID
 * @param int $money_id 钱包ID
 * @param int $money 变动金额
 * @param string|int $frozen 冻结钱包   传正数  释放钱包 传 负数
 * @param int $type 变动类型
 * @param string $note 备注
 * @param string|int $admin_id 管理员id
 * @return boolean
 */
function userMoneyAddLogForOne($user_id, $money_id, $money = 0, $frozen = 0, $type, $note = '', $admin_id = '') {
    $data['user_id'] = $user_id;
    $data['money_id'] = $money_id;
    $info = M(NUOYILIANNAME . '.users_money')->where($data)->find();
    if (!$info) {
        M(NUOYILIANNAME . '.users_money')->add($data);
    }
    $acc['money'] = $frozen;
    $acc['total'] = $info['money'] + $info['frozen'];
    if ($money != 0) {
        $acc['total'] += $money;
        $acc['money'] = $money;
    }
    $acc['user_id'] = $user_id;
    $acc['money_id'] = $money_id;
    if ($frozen != 0) {
        $money = '-' . $frozen;
    }
    $acc['is_type'] = $type;
    $acc['zf_time'] = time();
    $acc['note'] = $note;
    $acc['admin_id'] = $admin_id;
    $sql = "UPDATE " . NUOYILIANNAME . ".__PREFIX__users_money SET money = money + $money," .
            " frozen = frozen + $frozen,total = total + $frozen + $money  WHERE user_id = $user_id and money_id = $money_id";
    if (D(NUOYILIANNAME . '.users_money')->execute($sql)) {
        $res = M(NUOYILIANNAME . '.users_money_log')->add($acc);
        return $res;
    } else {
        return false;
    }
}

/**
 * 添加会员  钱包冻结数据
 * @param type $uId 会员id
 * @param type $Locknum 冻结数量
 * @param type $sharesId 股票id
 * @param type $type 冻结 分类
 * @param type $note 备注
 * @return type
 */
function lockUserMoneyAdd($uId, $Locknum, $mId = 1, $type = 1, $note = '') {
    $data['uid'] = $uId;
    $data['mid'] = $mId;
    $data['lock_time'] = time();
    $data['frozen'] = $Locknum;
    $data['statu'] = 2;
    $data['type'] = $type;
    $note && $data['note'] = $note;
    if (M('users_money_lock')->add($data)) {
        return $infoId = M('users_money')->where(array('mid' => $mId, 'uid' => $uId))->setInc('frozen', $Locknum);
    }
}

/**
 * 管理员批量会员提现
 * @param type $mId 提现钱包
 * @param type $isType 提现方式
 * @param type $add 金额
 * @param type $level 会员级别
 */
function listCarryAdd($mId, $isType, $add, $fee = 0, $level = 0) {
    $moneylist = M('users_money')->where(array("mid" => $mId))->select();
    if ($moneylist) {
        foreach ($moneylist as $v) {
            if ($isType == 1) {
                $money = ($v['money'] - $v['frozen']) - $add;
            } elseif ($isType == 2) {
                if ($v['money'] > $add) {
                    $money = $add;
                } else {
                    $money = 0;
                }
            } elseif ($isType == 3) {
                $money = ($v['money'] - $v['frozen']) * $add / 100;
            }
            if ($money > 0) {
                $mFee = 0;
                if ($fee > 0) {
                    $mFee = ($money * $fee) / 100;
                }
                $bankInfo = M('users_bank')->where(array('uid' => $v['uid']))->find();
                if ($bankInfo['opening_id'] > 0 && $bankInfo['bank_account'] > 0 && $bankInfo['bank_name'] != '') {
                    $data['uid'] = $v['uid'];
                    $data['mid'] = $mId;
                    $data['add_time'] = time();
                    $data['add_money'] = $money;
                    $data['fee'] = $fee;
                    $data['fee_money'] = $mFee;
                    $data['out_money'] = $money - $mFee;
                    $data['opening_id'] = $bankInfo['opening_id'];
                    $data['bank_account'] = $bankInfo['bank_account'];
                    $data['bank_name'] = $bankInfo['bank_name'];
                    $data['note'] = '批量提现';
                    $data['statu'] = 1;
                    if (M('money_carry_log')->add($data)) {
                        userMoneyLogAdd($v['uid'], $mId, '-' . $money, 104, '批量提现', session('admin_id'));
                    }
                }
            }
        }
    }
    return true;
}

/**
 * 安全播出计算
 */
function securityBochu() {
    return true;
    // 日播出计算
    $day = M('bochu_day')->where(array('zf_time' => strtotime(date('Y-m-d'))))->field('zhichu,shuoru')->find();
    $bochu = intVal($day['zhichu'] / $day['shuoru'] * 100);
    if (zfCache('securityInfo.day_bocu_total') > $bochu || zfCache('securityInfo.day_bocu_total') <= 0) {
        // 总播出 计算
        if (zfCache('securityInfo.bocu_total') > clear_bochu(6) || zfCache('securityInfo.bocu_total') <= 0) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

/**
 * 钱包管理
 * @param int $moneyId  钱包 id
 * @param int $a 类型
 * @return float|int
 */
function moneyList($mId, $a = 1) {
    $info = M('money')->where(array('money_id' => $mId))->cache(true)->find();
    if ($a == 1) {
        return $info['name_cn'];
    }
    if ($a == 2) {
        return $info['pre']; //
    }
    if ($a == 8) {
        return $info['c_pre'];
    }
    if ($a == 9) {
        return $info['t_pre'];
    }
}

/**
 * 奖金日志添加
 * @param type $uid  会员 ID  表示那个会员拿奖
 * @param type $bonus_id  奖金 ID
 * @param type $come_uid  当前奖项 来源于那一个会员 ID
 * @param type $money   奖金金额  没有扣税的  也没有 按比例分的 总奖金金额
 * @param type $note   备注
 * @param type $sj   结算方式  1 秒 2 日 3 周 4 月 5 季 6 年
 * @param type $statu   结算状态  1 待结算 2 结算中 9 己结算
 * @return boolean
 */
function bonusLogAdd($uid, $bonus_id, $come_uid, $money, $note, $sj, $statu = 1) {
    $add['uid'] = $uid;
    $add['bonus_id'] = $bonus_id;
    $add['come_uid'] = $come_uid;
    $add['money'] = $money;
    $add['note'] = $note;
    $add['add_time'] = time();
    $add['sj'] = $sj;
    $add['statu'] = $statu;
    $infoId = M('bonus_log')->add($add);
    if ($infoId) {
        return $infoId;
    } else {
        return FALSE;
    }
}

/**
 * 统一奖金分配方式
 * @param int $bonus 当前奖项数组
 * @param int $money 奖金金额
 * @param int $user_id 会员Id
 * @param string $note 说明备注
 * @param int $istype 类型 1动态 2静态
 * @return int
 */
function bonusSjUnified($bonus, $money, $userId, $note, $istype = 1, $come_user_id = '') {
    $text = $note;
    $model = M();
    $model->startTrans();
    $taxa = $taxb = $taxc = 0;
    $taxaId = $taxbId = $taxcId = $per1Id = $per2Id = $per3Id = 1;
    if ($bonus['t_1'] > 0 && $bonus['tp_1'] > 0) {
        $taxa = $money * $bonus['tp_1'] / 100;
        if ($taxa > 0) {
            usersDay($userId, 'tax_' . $bonus['t_1'], $taxa, 0, 0, 0, 0);
        }
        $taxaInfo = M('bonus_tax')->where("tax_id = {$bonus['t_1']}")->find();
        if ($taxaInfo['money_id'] > 0 && $taxa > 0) {
            $taxaId = userMoneyLogAdd($userId, $taxaInfo['money_id'], $taxa, $bonus['bonus_id'], $note, '', $come_user_id);
        }
        $text .= '扣' . $taxaInfo['name_cn'] . $taxa;
    }
    if ($bonus['t_2'] > 0 && $bonus['tp_2'] > 0) {
        $taxb = $money * $bonus['tp_2'] / 100;
        if ($taxb > 0) {
            usersDay($userId, 'tax_' . $bonus['t_2'], $taxb, 0, 0, 0, 0);
        }
        $taxbInfo = M('bonus_tax')->where("tax_id = {$bonus['t_2']}")->find();
        if ($taxbInfo['money_id'] > 0 && $taxb > 0) {
            $taxbId = userMoneyLogAdd($userId, $taxbInfo['money_id'], $taxb, $bonus['bonus_id'], $note, '', $come_user_id);
        }
        $text .= '扣' . $taxbInfo['name_cn'] . $taxb;
    }
    if ($bonus['t_3'] > 0 && $bonus['tp_3'] > 0) {
        $taxc = $money * $bonus['tp_3'] / 100;
        if ($taxc > 0) {
            usersDay($userId, 'tax_' . $bonus['t_3'], $taxc, 0, 0, 0, 0);
        }
        $taxcInfo = M('bonus_tax')->where("tax_id = {$bonus['t_3']}")->find();
        if ($taxcInfo['money_id'] > 0 && $taxc > 0) {
            $taxcId = userMoneyLogAdd($userId, $taxcInfo['money_id'], $taxc, $bonus['bonus_id'], $note, '', $come_user_id);
        }
        $text .= '扣' . $taxcInfo['name_cn'] . $taxc;
    }

    // 先算出扣税的金额
    $sFprice = $money - $taxa - $taxb - $taxc;
    if ($sFprice > 0) {
        if ($bonus['mp_1'] > 0 && $bonus['m_1'] > 0) {
            $per1 = $sFprice * $bonus['mp_1'] / 100;
            $note1 = $text . $sFprice . '*' . $bonus['mp_1'] . '%= ' . $per1;
            if ($per1 > 0) {
                $per1Id = userMoneyLogAdd($userId, $bonus['m_1'], $per1, $bonus['bonus_id'], $note1, '', $come_user_id);
            }
            if ($bonus['bonus_id'] == 1 || $bonus['bonus_id'] == 12) {
                $sharesPrice = sharesInfo(1);
                $sharesNume = $per1 / $sharesPrice['now_price'];
                sharesLog($userId, 1, $sharesNume, 0, 97, $note, 0, $userId);
                userMoneyLogAdd($userId, $bonus['m_1'], '-' . $per1, 115, '自动买入' . $sharesPrice['name_cn'], '', $come_user_id);
            }
        }
        if ($bonus['mp_2'] > 0 && $bonus['m_2'] > 0) {
            $per2 = $sFprice * $bonus['mp_2'] / 100;
            $note2 = $text . $sFprice . '*' . $bonus['mp_2'] . '%= ' . $per2;
            if ($per2 > 0) {
                $per2Id = userMoneyLogAdd($userId, $bonus['m_2'], $per2, $bonus['bonus_id'], $note2, '', $come_user_id);
            }
            if ($bonus['bonus_id'] == 1 || $bonus['bonus_id'] == 12) {
                $sharesPrice = sharesInfo(1);
                $sharesNume = $per2 / $sharesPrice['now_price'];
                sharesLog($userId, 1, $sharesNume, 0, 97, $note, 0, $userId);
                userMoneyLogAdd($userId, $bonus['m_2'], '-' . $per2, 115, '自动买入' . $sharesPrice['name_cn'], '', $come_user_id);
            }
        }
        if ($bonus['mp_3'] > 0 && $bonus['m_3'] > 0) {
            $per3 = $sFprice * $bonus['mp_3'] / 100;
            $note3 = $text . $sFprice . '*' . $bonus['mp_3'] . '%= ' . $per3;
            if ($per3 > 0) {
                $per3Id = userMoneyLogAdd($userId, $bonus['m_3'], $per3, $bonus['bonus_id'], $note3, '', $come_user_id);
            }
            if ($bonus['bonus_id'] == 1 || $bonus['bonus_id'] == 12) {
                $sharesPrice = sharesInfo(1);
                $sharesNume = $per3 / $sharesPrice['now_price'];
                sharesLog($userId, 1, $sharesNume, 0, 97, $note, 0, $userId);
                userMoneyLogAdd($userId, $bonus['m_3'], '-' . $per3, 115, '自动买入' . $sharesPrice['name_cn'], '', $come_user_id);
            }
        }
        $webbouchu = $userbouchu = false;
        if ($sFprice > 0) {
            $webbouchu = bochuDay('bonus_' . $bonus['bonus_id'], $sFprice); // 单个奖金记算拨出
            $userbouchu = usersDay($userId, 'bonus_' . $bonus['bonus_id'], $sFprice, $sFprice, $money);
        }
        if ($taxaId && $taxbId && $taxcId && $per1Id && $per2Id && $per3Id && $webbouchu && $userbouchu) {
            $model->commit();
            return 1;
        } else {
            $model->rollback();
            return 0;
        }
    }
}

/**
 * 推荐奖计算 （激活、复投
 * 激活:Zfuwl/Logic/ActivateLogic.class.php line:86
 * @param type $tjrId  推荐人 id
 * @param type $user_id 会员 id
 * @param type $yj  报单金额
 * @param type $note    激活会员
 * @param type $i
 * @return type
 */
function bonus1Clear($tjrId, $userId, $yj = 0, $note = '', $i = 0) {
    if (!$userId || !$tjrId && $tjrId != $userId) {
        return;
    }
    $tjrInfo = userInfo($tjrId);
    if ($tjrInfo) {
        $tjrlevel = levelInfo($tjrInfo['level']);
        $arr = explode(',', $tjrlevel['b_1']);
        $num = count($arr);
        $bonusInfo = bonusInfo(1);
        if ($bonusInfo['type'] == 1) {
            $money = $yj * $arr[$i] / 100; // 按比例分配
        } else {
            $money = $arr[$i]; // 如果是按 固定金额  就直接等于
        }
        $text = $note;
        if ($tjrInfo['trends'] == 1 && $tjrInfo['activate'] == 1 && $tjrInfo['frozen'] == 1 && $money > 0) {
            $logId = bonusLogAdd($tjrId, $bonusInfo['bonus_id'], $userId, $money, $text, $bonusInfo['sj']); // 记录奖金日志
            if ($bonusInfo['sj'] == 1 && $logId > 0 && securityBochu()) {
                if (bonusSjUnified($bonusInfo, $money, $tjrId, $bonusInfo[bonus_name] . $text, 2, $userId)) {
                    M('bonus_log')->where(array('id' => $logId))->save(array('statu' => 9, 'sj_time' => time()));
                }
            }
        }
        // if ($num >= $i) {
        //     bonus1Clear($tjrInfo['tjr_id'], $userId, $yj, $note, $i + 1);
        // }
    }
    return TRUE;
}

/**
 * 感恩奖计算
 * @param type $tjrId  推荐人 id
 * @param type $user_id 会员 id
 * @param type $yj  报单金额
 * @param type $note    激活会员
 * @param type $i
 * @return type
 */
function bonus12Clear($tjrId, $userId, $yj, $note = '', $i = 0) {
    if (!$userId || !$tjrId && $tjrId != $userId) {
        return;
    }
    $tjrInfo = userInfo($tjrId);
    if ($tjrInfo) {
        $tjrlevel = levelInfo($tjrInfo['level']);
        $arr = explode(',', $tjrlevel['b_12']);
        $num = count($arr);
        $bonusInfo = bonusInfo(12);
        if ($bonusInfo['type'] == 1) {
            $money = $yj * $arr[$i] / 100; // 按比例分配
        } else {
            $money = $arr[$i]; // 如果是按 固定金额  就直接等于
        }
        $text = $note;
        if ($tjrInfo['trends'] == 1 && $tjrInfo['activate'] == 1 && $tjrInfo['frozen'] == 1 && $money > 0) {
            $logId = bonusLogAdd($tjrId, $bonusInfo['bonus_id'], $userId, $money, $text, $bonusInfo['sj']); // 记录奖金日志
            if ($bonusInfo['sj'] == 1 && $logId > 0 && securityBochu()) {
                if (bonusSjUnified($bonusInfo, $money, $tjrId, $bonusInfo[bonus_name] . $text, 2, $userId)) {
                    M('bonus_log')->where(array('id' => $logId))->save(array('statu' => 9, 'sj_time' => time()));
                }
            }
        }
        // if ($num >= $i) {
        //     bonus1Clear($tjrInfo['tjr_id'], $userId, $yj, $note, $i + 1);
        // }
    }
    return TRUE;
}

/**
 * 代理奖结算(推荐代理)
 * @param type $tjrId  推荐人 id
 * @param type $user_id 会员 id
 * @param type $yj  报单金额
 * @param type $note    激活会员
 * @param type $i
 * @return type
 */
//function serviceClear($tjrId, $userId, $yj, $note = '') {
//    if (!$userId || !$tjrId && $tjrId != $userId) {
//        return;
//    }
//    $tjrInfo = userInfo($tjrId);
//    if ($tjrInfo && $tjrInfo['service'] > 0) {
//        $serviceInfo = M('service')->where(array('id' => $tjrInfo['service']))->find();
//        $bonusInfo = bonusInfo(13);
//        if ($bonusInfo['type'] == 1) {
//            $money = $yj * $serviceInfo['per'] / 100; // 按比例分配
//        } else {
//            $money = $serviceInfo['per']; // 如果是按 固定金额  就直接等于
//        }
//        $text = $note;
//        if ($tjrInfo['trends'] == 1 && $tjrInfo['activate'] == 1 && $tjrInfo['frozen'] == 1 && $money > 0) {
//            $logId = bonusLogAdd($tjrId, $bonusInfo['bonus_id'], $userId, $money, $text, $bonusInfo['sj']); // 记录奖金日志
//            if ($bonusInfo['sj'] == 1 && $logId > 0 && securityBochu()) {
//                if (bonusSjUnified($bonusInfo, $money, $tjrId, $bonusInfo[bonus_name] . $text, 2, $userId)) {
//                    M('bonus_log')->where(array('id' => $logId))->save(array('statu' => 9, 'sj_time' => time()));
//                }
//            }
//        }
//    }
//    return TRUE;
//}

/**
 * 代理奖结算(推荐代理)
 * @param type $tjrId  推荐人 id
 * @param type $user_id 会员 id
 * @param type $yj  报单金额
 * @param type $note    激活会员
 * @param type $i
 * @return type
 */
//function serviceClear($tjrId, $userId, $yj, $note = '') {
//    if (!$userId || !$tjrId && $tjrId != $userId) {
//        return;
//    }
//    $tjrInfo = userInfo($tjrId);
//    if ($tjrInfo && $tjrInfo['service'] > 0) {
//        $serviceInfo = M('service')->where(array('id' => $tjrInfo['service']))->find();
//        $sharesPrice = sharesInfo(1);
//        $money = $yj * $serviceInfo['per'] / 100;
//        if ($money > 0) {
//            usersDay($tjrId, 'service', $money, $money, $money);
//            bochuDay('service', $price);
//            $sharesNume = $money / $sharesPrice['now_price'];
//            sharesLog($tjrId, 1, $sharesNume, 0, 97, $note, 0, $userId);
//        }
//    }
//    return TRUE;
//}

/**
 * 代理奖结算(赠送下级)
 * @param type $tjrId  推荐人 id
 * @param type $user_id 会员 id
 * @param type $yj  报单金额
 * @param type $note    激活会员
 * @param type $i
 * @return type
 */
function serviceClearForXj($userId, $note = '') {
    if (!$userId) {
        return;
    }
    $userInfo = M('users')->where(array('user_id' => $userId))->field('service')->find();
    $serviceInfo = M('service')->where(array('id' => $userInfo['service']))->find();
    $userIdArr = nexttd($userId);
    $money = floatval($serviceInfo['team_money']);
    foreach ($userIdArr as $v) {
        if ($money > 0) {
            $sharesPrice = sharesInfo(1);
            sharesLog($v, 1, ($money / $sharesPrice['now_price']), 0, 1, $note, 0, $userId);
        }
    }
    return TRUE;
}

/**
 * 激活赠送会员诺一链
 * @param int $userId 赠送会员id
 * @param string $note 赠送备注默认激活赠送
 * @return bool
 */
function giveUserNuoyil($userId, $note = '激活赠送') {
    $money = floatval(zfCache('regInfo.jh_zs_nyl_num'));
    if ($money > 0) {
        sharesLog($userId, 1, $money, 0, 1, $note);
    }
    return true;
}

/**
 * 代理奖 (地址关系)
 * @param int $userId 会员id
 * @param float|int $yj 报单业绩
 * @param string $note 备注
 * @return bool
 */
function serviceUserAddress($userId, $yj, $note) {
    $userInfo = D("UserView")->where(array('user_id' => $userId))->field('province,city,district')->find();
    $bonusInfo = bonusInfo(13);
    # 省级代理
    if ($userInfo['province'] > 0) {
        $provinceList = D("UserView")->where(array('province' => $userInfo['province'], 'service' => 3))->field('user_id,trends,activate,frozen')->select();
        $serviceInfo = M('service')->where(array('id' => 3))->field('per')->find();
        if ($bonusInfo['type'] == 1) {
            $money = $yj * $serviceInfo['per'] / 100; // 按比例分配
        } else {
            $money = $serviceInfo['per']; // 如果是按 固定金额  就直接等于
        }
        foreach ($provinceList as $v) {
            $text = $note . $serviceInfo['name_cn'];
            if ($v['trends'] == 1 && $v['activate'] == 1 && $v['frozen'] == 1 && $money > 0) {
                $logId = bonusLogAdd($v['user_id'], $bonusInfo['bonus_id'], $userId, $money, $text, $bonusInfo['sj']); // 记录奖金日志
                if ($bonusInfo['sj'] == 1 && $logId > 0 && securityBochu()) {
                    if (bonusSjUnified($bonusInfo, $money, $v['user_id'], $bonusInfo[bonus_name] . $text, 2, $userId)) {
                        M('bonus_log')->where(array('id' => $logId))->save(array('statu' => 9, 'sj_time' => time()));
                    }
                }
            }
        }
    }
    # 市级代理
    if ($userInfo['city'] > 0) {
        $cityList = D("UserView")->where(array('city' => $userInfo['city'], 'service' => 2))->field('user_id,trends,activate,frozen')->select();
        $serviceInfo = M('service')->where(array('id' => 2))->field('per')->find();
        if ($bonusInfo['type'] == 1) {
            $money = $yj * $serviceInfo['per'] / 100; // 按比例分配
        } else {
            $money = $serviceInfo['per']; // 如果是按 固定金额  就直接等于
        }
        foreach ($cityList as $v) {
            $text = $note . $serviceInfo['name_cn'];
            if ($v['trends'] == 1 && $v['activate'] == 1 && $v['frozen'] == 1 && $money > 0) {
                $logId = bonusLogAdd($v['user_id'], $bonusInfo['bonus_id'], $userId, $money, $text, $bonusInfo['sj']); // 记录奖金日志
                if ($bonusInfo['sj'] == 1 && $logId > 0 && securityBochu()) {
                    if (bonusSjUnified($bonusInfo, $money, $v['user_id'], $bonusInfo[bonus_name] . $text, 2, $userId)) {
                        M('bonus_log')->where(array('id' => $logId))->save(array('statu' => 9, 'sj_time' => time()));
                    }
                }
            }
        }
    }
    #区级代理
    if ($userInfo['district'] > 0) {
        $districtList = D("UserView")->where(array('district' => $userInfo['district'], 'service' => 1))->field('user_id,trends,activate,frozen')->select();
        $serviceInfo = M('service')->where(array('id' => 1))->field('per')->find();
        if ($bonusInfo['type'] == 1) {
            $money = $yj * $serviceInfo['per'] / 100; // 按比例分配
        } else {
            $money = $serviceInfo['per']; // 如果是按 固定金额  就直接等于
        }
        foreach ($districtList as $v) {
            $text = $note . $serviceInfo['name_cn'];
            if ($v['trends'] == 1 && $v['activate'] == 1 && $v['frozen'] == 1 && $money > 0) {
                $logId = bonusLogAdd($v['user_id'], $bonusInfo['bonus_id'], $userId, $money, $text, $bonusInfo['sj']); // 记录奖金日志
                if ($bonusInfo['sj'] == 1 && $logId > 0 && securityBochu()) {
                    if (bonusSjUnified($bonusInfo, $money, $v['user_id'], $bonusInfo[bonus_name] . $text, 2, $userId)) {
                        M('bonus_log')->where(array('id' => $logId))->save(array('statu' => 9, 'sj_time' => time()));
                    }
                }
            }
        }
    }

    return true;
}
