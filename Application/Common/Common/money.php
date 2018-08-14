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
            $count = M('users_money')->where($data)->count();
            if ($count <= 0) {
                M('users_money')->add($data);
            }
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
 * @param int $mId  钱包 id
 * @return array 钱包信息
 */
function moneyOne($mId) {
    return M('money')->where(['money_id' => $mId])->cache('moneyOne' . $mId)->find();
}

function blockOne($bId) {
    return M('block')->where(array('id' => $bId))->find();
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
    $acc['per'] = blockList(1, 2);
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
 * @param int $uid  会员 ID  表示那个会员拿奖
 * @param int $bonus_id  奖金 ID
 * @param int $come_uid  当前奖项 来源于那一个会员 ID
 * @param int|floatval $money   奖金金额  没有扣税的  也没有 按比例分的 总奖金金额
 * @param string $note   备注
 * @param int $sj   结算方式  1 秒 2 日 3 周 4 月 5 季 6 年
 * @param int $statu   结算状态  1 待结算 2 结算中 9 己结算
 * @param float $per 释放比例
 * @return bool|int 成功返回奖金记录 错误返回false
 */
function bonusLogAdd($uid, $bonus_id, $come_uid, $money, $note, $sj, $statu = 1, $per = 100) {
    $data = [
        'uid' => (int) $uid
        , 'bonus_id' => (int) $bonus_id
        , 'come_uid' => (int) $come_uid
        , 'money' => (float) $money
        , 'note' => $note
        , 'add_time' => time()
        , 'sj' => (int) $sj
        , 'statu' => (int) $statu
        , 'per' => (float) $per
    ];
    $infoId = M('bonus_log')->add($data);
    if ($infoId) {
        return $infoId;
    } else {
        return false;
    }
}

/**
 * 查询会员货币可用余额
 * @param int $uId 会员 ID
 * @param int $mId 钱包 ID
 * @param int $a   1 查询可用余额   2  查询冻结金额 100所有钱包总和
 * @return float|int 金额
 */
function userBlock($uId, $bId, $a = 1) {
    if ($a == 100) {
        return floatval(M('block_user')->where(array('uid' => $uId))->sum('money'));
    }
    $info = M('block_user')->where(array('uid' => $uId, 'bid' => $bId))->find();
    if ($info) {
        if ($a == 1) {
            return $info['money'];
        }
        if ($a == 2) {
            return $info['frozen'];
        }
        if ($a == 3) {
            return $info['money'] - $info['frozen'];
        }
    } else {
        return 0;
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
    $taxaId = $taxbId = $taxcId = $per1Id = $per2Id = $per3Id = $per4Id = 1;
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

    # 见点奖封顶
    // if($bonusInfo['bonus_id'] == 2) {
    //     # 获取会员等级信息
    //     $userInfo = M('users')->where(['user_id' => $userId])->field('level')->find();
    //     $levelInfo = M('level')->where(['level_id' => $userInfo['level']])->field('amount,out_reg,b_2_total_bei')->find();
    //     # 会员见点奖封顶金额
    //     $fdMoney = $levelInfo['amount']*$levelInfo['b_2_total_bei'];
    //     # 会员已获得的见点奖
    //     $bonus2Money = floatval(M('money_log')->where(['uid' => $userId, 'money' => ['gt', 0], 'is_type' => 2])->sum('money'));
    //     if($bonus2Money + $sFprice > $fdMoney) {
    //         $sFprice = $fdMoney-$bonus2Money;
    //     }
    // }

    if ($sFprice > 0) {
        if ($bonus['mp_1'] > 0 && $bonus['m_1'] > 0) {
            $per1 = $sFprice * $bonus['mp_1'] / 100;
            $note1 = $text;
//            $note1 = $text . $sFprice . '*' . $bonus['mp_1'] . '%= ' . $per1;
            if ($per1 > 0) {
                $per1Id = userMoneyLogAdd($userId, $bonus['m_1'], $per1, $bonus['bonus_id'], $note1, '', $come_user_id);
            }
            // if ($bonus['bonus_id'] == 1 || $bonus['bonus_id'] == 12) {
            //     $sharesPrice = sharesInfo(1);
            //     $sharesNume = $per1 / $sharesPrice['now_price'];
            //     sharesLog($userId, 1, $sharesNume, 0, 97, $note . '自动买入', 0, $userId);
            //     userMoneyLogAdd($userId, $bonus['m_1'], '-' . $per1, 115, '自动买入' . $sharesPrice['name_cn'], '', $come_user_id);
            // }
        }
        if ($bonus['mp_2'] > 0 && $bonus['m_2'] > 0) {
            $per2 = $sFprice * $bonus['mp_2'] / 100;
            $note2 = $text;
//            $note2 = $text . $sFprice . '*' . $bonus['mp_2'] . '%= ' . $per2;
            if ($per2 > 0) {
                $per2Id = userMoneyLogAdd($userId, $bonus['m_2'], $per2, $bonus['bonus_id'], $note2, '', $come_user_id);
            }
            // if ($bonus['bonus_id'] == 1 || $bonus['bonus_id'] == 12) {
            //     $sharesPrice = sharesInfo(1);
            //     $sharesNume = $per2 / $sharesPrice['now_price'];
            //     sharesLog($userId, 1, $sharesNume, 0, 97, $note . '自动买入', 0, $userId);
            //     userMoneyLogAdd($userId, $bonus['m_2'], '-' . $per2, 115, '自动买入' . $sharesPrice['name_cn'], '', $come_user_id);
            // }
        }
        if ($bonus['mp_3'] > 0 && $bonus['m_3'] > 0) {
            $per3 = $sFprice * $bonus['mp_3'] / 100;
            $note3 = $text;
//            $note3 = $text . $sFprice . '*' . $bonus['mp_3'] . '%= ' . $per3;
            if ($per3 > 0) {
                $per3Id = userMoneyLogAdd($userId, $bonus['m_3'], $per3, $bonus['bonus_id'], $note3, '', $come_user_id);
            }
            // if ($bonus['bonus_id'] == 1 || $bonus['bonus_id'] == 12) {
            //     $sharesPrice = sharesInfo(1);
            //     $sharesNume = $per3 / $sharesPrice['now_price'];
            //     sharesLog($userId, 1, $sharesNume, 0, 97, $note3 . '自动买入', 0, $userId);
            //     userMoneyLogAdd($userId, $bonus['m_3'], '-' . $per3, 115, '自动买入' . $sharesPrice['name_cn'], '', $come_user_id);
            // }
        }
        if ($bonus['mp_4'] > 0 && $bonus['m_4'] > 0) {
            $per4 = $sFprice * $bonus['mp_4'] / 100;
            $note4 = $text;
//            $note4 = $text . $sFprice . '*' . $bonus['mp_4'] . '%= ' . $per4;
            if ($per4 > 0) {
                $per4Id = userMoneyLogAdd($userId, $bonus['m_4'], $per4, $bonus['bonus_id'], $note4, '', $come_user_id);
            }
            // if ($bonus['bonus_id'] == 1 || $bonus['bonus_id'] == 12) {
            //     $sharesPrice = sharesInfo(1);
            //     $sharesNume = $per3 / $sharesPrice['now_price'];
            //     sharesLog($userId, 1, $sharesNume, 0, 97, $note3 . '自动买入', 0, $userId);
            //     userMoneyLogAdd($userId, $bonus['m_3'], '-' . $per3, 115, '自动买入' . $sharesPrice['name_cn'], '', $come_user_id);
            // }
        }
        $webbouchu = $userbouchu = false;
        if ($sFprice > 0) {
            $webbouchu = bochuDay('bonus_' . $bonus['bonus_id'], $sFprice); // 单个奖金记算拨出
            $userbouchu = usersDay($userId, 'bonus_' . $bonus['bonus_id'], $sFprice, $sFprice, $money);
        }
        if ($taxaId && $taxbId && $taxcId && $per1Id && $per2Id && $per3Id && $per4Id && $webbouchu && $userbouchu) {
            $model->commit();
//            if (in_array($bonus['bonus_id'], [1, 2, 3])) {
//                judgeCj($userId);
//            }
            return 1;
        } else {
            $model->rollback();
            return 0;
        }
    }
}

/**
 * 推广奖
 * @param $tjrId 推荐人id
 * @param $userId 用户的id
 * @param $yj 金额
 * @param string $note 备注
 * @param int $i
 * @return true
 */
/**
 * 推广奖
 * @param $tjrId 推荐人id
 * @param $userId 用户的id
 * @param $yj 金额
 * @param string $note 备注
 * @param int $i
 * @return true
 */
function bonus1Clear($tjrId, $userId, $yj, $note = '', $i = 0) {
    if (!$userId || !$tjrId) {
        return;
    }
    $countNum = M('block_log')->where(['uid' => $userId, 'is_type' => 110])->count();
    if ($countNum == 1) {
        $tjrInfo = M('users')->where('user_id=' . $tjrId)->field('tjr_id,level,trends,activate,frozen,invest_money')->find();
        if ($tjrInfo['invest_money'] < $yj) {
            $yj = $tjrInfo['invest_money'];
        }
        // 判断该推荐人是否可以拿管理奖
        if ($tjrInfo && $tjrInfo['level'] >= 3) {
            $tjrlevel = levelInfo($tjrInfo['level']);
            $arr = explode(',', $tjrlevel['b_1']);
            $num = count($arr);
            $bonusInfo = bonusInfo(1);
            if ($bonusInfo['type'] == 1) {
                $money = $yj * $arr[$i] / 100; // 按比例分配
            } else {
                $money = $arr[$i]; // 如果是按 固定金额  就直接等于
            }
            $text = $note . '第' . ($i + 1) . '代';
            if ($i < $num && $tjrInfo['trends'] == 1 && $tjrInfo['activate'] == 1 && $tjrInfo['frozen'] == 1 && $money > 0) {
                $logId = bonusLogAdd($tjrId, $bonusInfo['bonus_id'], $userId, $money, $text, $bonusInfo['sj']); // 记录奖金日志
                if ($bonusInfo['sj'] == 1 && $logId > 0 && securityBochu()) {
                    if (bonusSjUnified($bonusInfo, $money, $tjrId, $bonusInfo['name_cn'] . $text, 2, $userId)) {
                        M('bonus_log')->where(array('id' => $logId))->save(array('statu' => 9, 'sj_time' => time()));
                    }
                }
            }
            if ($num >= ($i + 1)) {
                bonus1Clear($tjrInfo['tjr_id'], $userId, $yj, $note, $i + 1);
            }
        }
    }
    return TRUE;
}

/**
 * 储存奖
 * @return true
 */
/**
 * 储存奖
 * @return true
 */
function bonus2Clear($uid = 0) {
    if ($uid > 0) {
        $user = M('users')->where(['invest_money' => ['gt', 0], 'user_id' => $uid])->field('user_id,level,trends,activate,frozen,invest_money')->select();
    } else {
        $user = M('users')->where(['invest_money' => ['gt', 0]])->field('user_id,level,trends,activate,frozen,invest_money')->select();
    }
    foreach ($user as $v) {
        $tjrlevel = levelInfo($v['level']);
        $arr = explode(',', $tjrlevel['b_2']);
        $bonusInfo = bonusInfo(2);
        if ($bonusInfo['type'] == 1) {
            $money = $v['invest_money'] * $arr[0] / 1000; // 按比例分配
        } else {
            $money = $arr[0]; // 如果是按 固定金额  就直接等于
        }
        if ($v['trends'] == 1 && $v['activate'] == 1 && $v['frozen'] == 1 && $money > 0) {
            $logId = bonusLogAdd($v['user_id'], $bonusInfo['bonus_id'], $v['user_id'], $money, $bonusInfo['name_cn'], $bonusInfo['sj']); // 记录奖金日志
            if ($bonusInfo['sj'] == 1 && $logId > 0 && securityBochu()) {
                if (bonusSjUnified($bonusInfo, $money, $v['user_id'], $bonusInfo['name_cn'] . $text, 2, $v['user_id'])) {
                    M('bonus_log')->where(array('id' => $logId))->save(array('statu' => 9, 'sj_time' => time()));
                }
            }
        }
    }

    return TRUE;
}

/**
 * 分享奖1
 * @param $uid 用户的id
 */
//function bonus3Clear($uid) {
//    $user = M('users')->where(['user_id' => $uid])->field('leader,trends,activate,frozen')->find();
//    $leader = M('leader')->where(['statu' => 1])->getField('id,generalize_per');
//    if ($user['leader'] >= 1) {
//        $bonusInfo = bonusInfo(3);
//        $yi = tjrNumMoney($uid);
//        if ($bonusInfo['type'] == 1) {
//            $money = $yi * $leader[$user['leader']] / 100; // 按比例分配
//        } else {
//            $money = $leader[$user['leader']]; // 如果是按 固定金额  就直接等于
//        }
//        if ($user['trends'] == 1 && $user['activate'] == 1 && $user['frozen'] == 1 && $money > 0) {
//            $logId = bonusLogAdd($uid, $bonusInfo['bonus_id'], $uid, $money, '分红积分奖', $bonusInfo['sj']); // 记录奖金日志
//            if ($bonusInfo['sj'] == 1 && $logId > 0 && securityBochu()) {
//                if (bonusSjUnified($bonusInfo, $money, $uid, $bonusInfo['name_cn'], 2, $uid)) {
//                    M('bonus_log')->where(array('id' => $logId))->save(array('statu' => 9, 'sj_time' => time()));
//                }
//            }
//        }
//    }
//}

/**
 * 分享奖2
 * @param $uid 用户的id
 */
function bonus3Clear($uid=0) {
    if ($uid > 0) {
        $user = M('users')->where(['user_id' => $uid, 'leader' => ['gt', 0]])->field('user_id,leader,trends,activate,frozen')->select();
    } else {
        $user = M('users')->where(['leader' => ['gt', 0]])->field('user_id,leader,trends,activate,frozen')->select();
    }
  
    $leader = M('leader')->where(['statu' => 1])->getField('id,generalize_per');
    foreach ($user as $v) {
        if ($v['leader'] >= 1) {
            $bonusInfo = bonusInfo(3);
          
          
          /**
            $yi = tjrNumMoney($v['user_id']);
            if ($v['leader'] == 1) {
                if ($bonusInfo['type'] == 1) {
                    $money = $yi * $leader[$v['leader']] / 100; // 按比例分配
                } else {
                    $money = $leader[$v['leader']]; // 如果是按 固定金额  就直接等于
                }
            } else {
                if ($bonusInfo['type'] == 1) {
                    $money = $yi * ($leader[$v['leader']] - $leader[$v['leader'] - 1]) / 100; // 按比例分配
                } else {
                    $money = $leader[$v['leader']] - $leader[$v['leader'] - 1]; // 如果是按 固定金额  就直接等于
                }
            }
            */
          	$yi = tjrNumMoneyRange($v['user_id'], $v['leader']);
            if ($bonusInfo['type'] == 1) {
                $money = $yi * $leader[$v['leader']] / 100; // 按比例分配
            } else {
                $money = $leader[$v['leader']]; // 如果是按 固定金额  就直接等于
            }
            $eqyi = tjrNumMoneyEq($v['user_id'], $v['leader']);
            if ($eqyi > 0) {
                $moneyeq = $yi * 0.0005; // 按比例分配
            } else {
                $moneyeq = 0; // 按比例分配
            }
            $money = $money + $moneyeq;
            if ($v['trends'] == 1 && $v['activate'] == 1 && $v['frozen'] == 1 && $money > 0) {
                $logId = bonusLogAdd($v['user_id'], $bonusInfo['bonus_id'], $v['user_id'], $money, '分红积分奖', $bonusInfo['sj']); // 记录奖金日志
                if ($bonusInfo['sj'] == 1 && $logId > 0 && securityBochu()) {
                    if (bonusSjUnified($bonusInfo, $money, $v['user_id'], $bonusInfo['name_cn'], 2, $v['user_id'])) {
                        M('bonus_log')->where(array('id' => $logId))->save(array('statu' => 9, 'sj_time' => time()));
                    }
                }
            }
        }
    }
}
/**
 * @param $uid 用户的id
 * 查出用户第一代与第二代的总业绩
 */
function tjrNumMoney($uid) {
    $tjrUser = M('users')->where(['tjr_id' => $uid])->field('user_id')->select();
    foreach ($tjrUser as $v) {
        $arr[] = $v['user_id'];
        $tjrTjr = M('users')->where(['tjr_id' => $v['user_id']])->field('user_id')->select();
        foreach ($tjrTjr as $va) {
            $arr[] = $va['user_id'];
        }
    }

    if ($arr) {
        $data = [];
        $data['user_id'] = ['in', $arr];
     //   $data['bid'] = 1;
      //  $blockAll = M('block_user')->where($data)->sum('deposit');
      	$blockAll = M('users')->where($data)->sum('invest_money');
        return $blockAll;
    }
    return 0;
}



/**
 * @param $uid 用户的id
 * 查出用户第一代与第二代的总业绩 取极差
 */
function tjrNumMoneyRange($uid, $leader) {
    $tjrUser = M('users')->where(['tjr_id' => $uid])->field('user_id,leader')->select();
    foreach ($tjrUser as $v) {
        if ($leader > $v['leader']) {
            $arr[] = $v['user_id'];
          	$tjrTjr = M('users')->where(['tjr_id' => $v['user_id']])->field('user_id,leader')->select();
            foreach ($tjrTjr as $va) {
                if ($leader > $va['leader']) {
                    $arr[] = $va['user_id'];
                }
            }
        }
    }

    if ($arr) {
        $data = [];
        $data['user_id'] = ['in', $arr];
        $blockAll = M('users')->where($data)->sum('invest_money');
        return $blockAll;
    }
    return 0;
}

/**
 * @param $uid 用户的id
 * 查出用户第一代与第二代的总业绩 级别相同的业绩
 */
function tjrNumMoneyEq($uid, $leader) {
  	$arr[]= '';
    $tjrUser = M('users')->where(['tjr_id' => $uid])->field('user_id,leader')->select();
    foreach ($tjrUser as $v) {
        if ($leader == $v['leader']) {
            $arr[] = $v['user_id'];
          	$tjrTjr = M('users')->where(['tjr_id' => $v['user_id']])->field('user_id,leader')->select();
            foreach ($tjrTjr as $va) {
                if ($leader == $va['leader']) {
                    $arr[] = $va['user_id'];
                }
            }
        }
    }
    if ($arr) {
        $data = [];
        $data['user_id'] = ['in', $arr];
        $blockAll = M('users')->where($data)->sum('invest_money');
        return $blockAll;
    }
    return 0;
}




///**
// * 储存奖
// * @param $tjrId 推荐人id
// * @param $userId 用户的id
// * @param $yj 金额
// * @param string $note 备注
// * @param int $i
// * @return true
// */
//function bonus3Clear($tjrId, $userId, $yj, $note = '', $i = 0) {
//    if (!$userId || !$tjrId) {
//        return;
//    }
//
//    $tjrInfo = M('users')->where('user_id=' . $tjrId)->field('tjr_id,level,trends,activate,frozen')->find();
//    // 判断该推荐人是否可以拿管理奖
//    if ($tjrInfo && $tjrInfo['level'] >= 3) {
//        $tjrlevel = levelInfo($tjrInfo['level']);
//        $arr = explode(',', $tjrlevel['b_2']);
//        $num = count($arr);
//        $bonusInfo = bonusInfo(2);
//        if ($bonusInfo['type'] == 1) {
//            $money = $yj * $arr[$i] / 100; // 按比例分配
//        } else {
//            $money = $arr[$i]; // 如果是按 固定金额  就直接等于
//        }
//        $text = $note . '第' . ($i + 1) . '代';
//        if ($i < $num && $tjrInfo['trends'] == 1 && $tjrInfo['activate'] == 1 && $tjrInfo['frozen'] == 1 && $money > 0) {
//            $logId = bonusLogAdd($tjrId, $bonusInfo['bonus_id'], $userId, $money, $text, $bonusInfo['sj']); // 记录奖金日志
//            if ($bonusInfo['sj'] == 1 && $logId > 0 && securityBochu()) {
//                if (userBlockLogAdd($tjrId, 1, $money, 2, '储存奖')) {
//                    M('bonus_log')->where(array('id' => $logId))->save(array('statu' => 9, 'sj_time' => time()));
//                }
//            }
//        }
//
//        if ($num >= ($i + 1)) {
//            bonus2Clear($tjrInfo['tjr_id'], $userId, $yj, $note, $i + 1);
//        }
//    }
//    return TRUE;
//}
