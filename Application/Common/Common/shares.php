<?php

header('Content-Type:text/html;Charset=UTF-8');

/**
 * 添加股票的时候 所有的会员 都添加一个股票字段
 * @param type $userId
 */
function sharesUserAdd($sId) {
    $list = M('users')->field('user_id')->select();
    if ($list) {
        foreach ($list as $v) {
            $data['uid'] = $v['user_id'];
            $data['sid'] = $sId;
            M('shares_user')->add($data);
        }
    }
    return TRUE;
}
/**
 * 查询
 */
function sharesname($sharesId){
    return M(NUOYILIANNAME.'.shares')->where(array('id' => $sharesId))->field('name')->find();
}
/**
 * 会员股票变动记录
 * @param int $user_id  会员id
 * @param int $shares_id  股票id
 * @param float|int $money   变动金额
 * @param float|int $frozen 冻结股票   传正数  释放股票 传 负数
 * @param int $type   变动属性
 * @param string $note 备注
 * @param int $admin_id 管理员id
 * @param int $come_user_id 来自哪个会员id
 * @return boolean
 */
function sharesLog($user_id, $shares_id, $money = 0, $frozen = 0, $type, $note = '', $admin_id = 0, $come_user_id = 0) {
    $data['user_id'] = $user_id;
    $data['shares_id'] = $shares_id;
    $data['pt'] = PTVAL;
    $info = M(NUOYILIANNAME.'.shares_user')->where($data)->find();
    if (!$info) {
        M(NUOYILIANNAME.'.shares_user')->add($data);
    }
    $acc['money'] = $frozen;
    $acc['total'] = $info['money'] + $info['frozen'];
    if ($money != 0) {
        $acc['total'] += $money;
        $acc['money'] = $money;
    }
    $acc['user_id'] = $user_id;
    $acc['shares_id'] = $shares_id;
    if ($frozen != 0) {
        $money = '-' . $frozen;
    }
    $acc['is_type'] = $type;
    $acc['zf_time'] = time();
    $acc['note'] = $note;
    $acc['admin_id'] = $admin_id ? $admin_id : FALSE;
    $acc['come_user_id'] = $come_user_id ? $come_user_id : FALSE;
    $sql = "UPDATE ".NUOYILIANNAME.'.' . C('DB_PREFIX') . "shares_user SET money = money + $money," .
            " frozen = frozen + $frozen,total = total + $frozen + $money  WHERE user_id = $user_id and shares_id = $shares_id and pt=".PTVAL;
    if (D(NUOYILIANNAME.'.shares_user')->execute($sql)) {
        $acc['pt'] = PTVAL;
        $res = M(NUOYILIANNAME.'.shares_log')->add($acc);
        return $res;
    } else {
        return false;
    }
}
/**
 * 会员股票变动记录
 * @param int $user_id  会员id
 * @param int $shares_id  股票id
 * @param float|int $money   变动金额
 * @param float|int $frozen 冻结股票   传正数  释放股票 传 负数
 * @param int $type   变动属性
 * @param string $note 备注
 * @param int $admin_id 管理员id
 * @param int $come_user_id 来自哪个会员id
 * @return boolean
 */
function sharesLogForNext($user_id, $shares_id, $money = 0, $frozen = 0, $type, $note = '', $admin_id = 0, $come_user_id = 0) {
    $data['user_id'] = $user_id;
    $data['shares_id'] = $shares_id;
    $data['pt'] = PTVALFORNEXT;
    $info = M(NUOYILIANNAME.'.shares_user')->where($data)->find();
    if (!$info) {
        M(NUOYILIANNAME.'.shares_user')->add($data);
    }
    $acc['money'] = $frozen;
    $acc['total'] = $info['money'] + $info['frozen'];
    if ($money != 0) {
        $acc['total'] += $money;
        $acc['money'] = $money;
    }
    $acc['user_id'] = $user_id;
    $acc['shares_id'] = $shares_id;
    if ($frozen != 0) {
        $money = '-' . $frozen;
    }
    $acc['is_type'] = $type;
    $acc['zf_time'] = time();
    $acc['note'] = $note;
    $acc['admin_id'] = $admin_id ? $admin_id : FALSE;
    $acc['come_user_id'] = $come_user_id ? $come_user_id : FALSE;
    $sql = "UPDATE ".NUOYILIANNAME.'.' . C('DB_PREFIX') . "shares_user SET money = money + $money," .
            " frozen = frozen + $frozen,total = total + $frozen + $money  WHERE user_id = $user_id and shares_id = $shares_id and pt=".PTVALFORNEXT;
    if (D(NUOYILIANNAME.'.shares_user')->execute($sql)) {
        $acc['pt'] = PTVALFORNEXT;
        $res = M(NUOYILIANNAME.'.shares_log')->add($acc);
        return $res;
    } else {
        return false;
    }
}

/**
 * 添加会员  股票冻结数据
 * @param type $uId 会员id
 * @param type $Locknum 冻结数量
 * @param type $sharesId 股票id
 * @param type $type 冻结 分类
 * @param type $note 备注
 * @return type
 */
function lockUserSharesAdd($uId, $Locknum, $sId = 1, $type = 1, $note = '') {
    $data = [
        'uid' => $uId
        ,'sid' => $sId
        ,'lock_time' => time()
        ,'frozen' => $Locknum
        ,'statu' => 2
        ,'type' => $type
        ,'note' => $note
        ,'sf_per' => floatval(zfCache('securityInfo.sf_per'))
        ,'sf_money' => floatval($Locknum*zfCache('securityInfo.sf_per')/100)
    ];
    // $note && $data['note'] = $note;
    if (M('shares_user_lock')->add($data)) {
        $where = [
            'user_id' => $uId
            ,'shares_id' => $sId
            ,'pt' => PTVALFORNEXT
        ];
        $info = M(NUOYILIANNAME.'.shares_user')->where($where)->find();
        if (!$info) {
            M(NUOYILIANNAME.'.shares_user')->add($where);
        }
        return $infoId = M('shares_user')->where(['shares_id' => $sId, 'user_id' => $uId])->setInc('frozen', $Locknum);
    }
}

function sharesLogType($id) {
    $data = array();
    $data[1] = "赠送";
    $data[2] = "买入";
    $data[3] = "卖出";
    $data[4] = "拆送";
    $data[5] = "冻结";
    $data[6] = "释放";
    $data[7] = "管理员操作";
    $data[8] = "变更资料";
    $data[96] = "管理调整";
    $data[97] = "推荐奖";
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

function sharesType($id) {
    $data = array();
    $data[1] = "启用";
    $data[2] = "关闭";
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

function sellBuyStatus($id) {
    $data = array();
    $data[1] = "等待交易";
    $data[2] = "交易中";
    $data[3] = "己撤 销";
    $data[9] = "交易成功";
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

/**
 * 股票发行 状态
 * @param type $id
 * @return string
 */
function sharesIssueIsType($id) {
    $data = array();
    $data[1] = "正在发行";
    $data[9] = "己售完";
    $data[3] = "己回购";
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

/**
 * 自动涨价规则
 * @param type $id
 * @return string
 */
function sharesRiseType($id) {
    $data = array();
    $data[1] = "发行交易数量涨价";
    $data[2] = "固定交易周期涨价";
    $data[3] = "累计交易数量涨价";
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

/**
 * 价格变动规则1 自动按发行数量涨价 2 自动按周期涨价 3 手动涨价
 * @param type $id
 * @return string
 */
function sharesPriceType($id) {
    $data = array();
    $data[1] = "发行数量自动";
    $data[2] = "固定周期自动";
    $data[3] = "管理手动";
    $data[4] = "手动拆分";
    $data[5] = "自动拆分";
    $data[6] = "交易数量自动";
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

/**
 * @param type $id
 * @return string
 */
function isAtuo($id) {
    $data = array();
    $data[1] = "自动";
    $data[2] = "手动";
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

function sharesInfo($id) {
    return $info = M(NUOYILIANNAME.'.shares')->where(array('id' => $id))->find();
}


/**
 *  价格变动的情况下 统一调整会员买入股票的数量
 * @param type $sharesId  股票 id
 * @param type $price  股票价格
 */
function unifiedBuyShanes($sharesId, $price) {
    set_time_limit(86400);
    $list = M(NUOYILIANNAME.'.shares_buy')->where(array('shares_id' => $sharesId, 'price' => array('neq', $price), 'status' => array('elt', 3)))->select();
    if ($list) {
        $config = M(NUOYILIANNAME.'.shares_config')->where(array('shares_id' => $sharesId, 'is_type' => 1))->find();
        foreach ($list as $v) {
            $bMoney = $bNum = $Cm = $userBuyNum = $disparity = 0;
            # 2777 - 1959 = 818
            $disparity = $v['num'] - $v['out_num'];
            # 818   * 0.18 = 147.24
            if ($disparity > 0) {
                $bMoney = ($disparity * $v['price']) + (($disparity * $v['price']) * $config['buy_fee'] / 100);
                $userBuyNum = intval(($v['total'] - $bMoney) / ($price + ($price * $config['buy_fee'] / 100)));
            } else {
                $userBuyNum = intval($v['total'] / ($price + ($price * $config['buy_fee'] / 100)));
            }
            #账户余额
            if($v['pt'] == PTVAL) {
                $userMoney = usersMoney($v['user_id'], $config['money_id']);
            } else {
                $userMoney = usersMoneyForNext($v['user_id'], $config['money_id']);
            }
            #当前股的数量  重新调整的可购买量
//ID 10253三钻以买入818*0.18 = 147.24  排单价格是  499.86 - 147.24 = 352.62 / 0.19 = 1855  余 0.17  + 之前的余额 0.14 = 0.31 / 0.19 = 1  余 0.12
            $buyMoney = ($userBuyNum * $price) + ($userBuyNum * $config['buy_fee'] / 100);
            if ($buyMoney < $v['total']) {
                # 0.17  =
                $money = $v['total'] - $buyMoney - $bMoney;
                $bNum = intval(($money + $userMoney) / $price);
                $Cm = $bNum * $price;
                if (($bNum * $price) > $money) {
                    $jMoney = ($bNum * $price) - $money;
                    if($v['pt'] == PTVAL) {
                        userMoneyLogAdd($v['user_id'], $config['money_id'], '-' . $jMoney, 120, '排队价格变动补差价');
                        // userMoneyAddLog($v['user_id'], $config['money_id'], '-' . $jMoney, 0, 117, '排队价格变动补差价');
                    } else {
                        userMoneyAddLogForOne($v['user_id'], $config['money_id'], '-' . $jMoney, 0, 117, '排队价格变动补差价');
                    }
                } else {
                    if ($money > 0) {
                        if($v['pt'] == PTVAL) {
                            userMoneyLogAdd($v['user_id'], $config['money_id'], $money, 120, '排队价格变动补差价');
                        } else {
                            userMoneyAddLogForOne($v['user_id'], $config['money_id'], $money, 0, 117, '排队价格变动重新调整退回');
                        }
                        // userMoneyAddLog($v['user_id'], $config['money_id'], $money, 0, 117, '排队价格变动重新调整退回');
                    }
                }
                M(NUOYILIANNAME.'.shares_buy')->where(array('id' => $v['id']))->save(array('out_num' => ($userBuyNum + $bNum), 'price' => $price, 'total' => ($buyMoney + $bMoney + $Cm), 'num' => ($userBuyNum + $bNum + $disparity)));
            } else {
                if (($buyMoney + $bMoney) == $v['total']) {
                    M(NUOYILIANNAME.'.shares_buy')->where(array('id' => $v['id']))->save(array('out_num' => $userBuyNum, 'price' => $price, 'num' => ($userBuyNum + $disparity)));
                } else {
                    M(NUOYILIANNAME.'.shares_buy')->where(array('id' => $v['id']))->save(array('out_num' => $userBuyNum, 'price' => $price, 'total' => ($buyMoney + $bMoney + $Cm), 'num' => ($userBuyNum + $disparity)));
                }
            }
            sharesAddPp($v['user_id'], $v['id'], $config);
        }
    }
    return TRUE;
}


/**
 *  卖出多单匹配交易
 * @param type $userId 会员id
 * @param type $id  买入数据的当条id
 * @return type
 */
function sharesOutPp($userId, $id, $config) {
//    set_time_limit(86400);
    $sellInfo = M(NUOYILIANNAME.'.shares_sell')->where(array('id' => $id, 'user_id' => $userId, 'pt' => PTVAL))->find(); // 当条购买数据记录
    $sharesInfo = getSharesInfo($sellInfo['shares_id']);
    if ($sellInfo['out_num'] > 0 && $sellInfo['status'] < 3) {
// 如果公司的己出售完成 或者未开启交易 就查出其它会员出售的价格与本条要购买价格一致的数据
//        if (zfCache('securityInfo.gupiao_rc') == 1) {
//            $buyInfo = M('shares_buy')->where(array('user_id' => array('neq', $userId), 'price' => $sellInfo['price'], 'status' => array('lt', 3), 'shares_id' => $sellInfo['shares_id'], 'zf_time' => array('elt', time() - zfCache('securityInfo.gupiao_rc_time') * 86400)))->order('sort desc')->limit('1')->find();
//        } else {
            $buyInfo = M(NUOYILIANNAME.'.shares_buy')->where(array('user_id' => array('neq', $userId), 'price' => $sellInfo['price'], 'status' => array('lt', 3), 'shares_id' => $sellInfo['shares_id'], 'pt' => PTVAL))->order('zf_time asc')->limit('1')->find();
//        }
        if ($buyInfo['out_num'] > 0 && $sellInfo['out_num'] > 0) {
            $Pdnum = min($sellInfo['out_num'], $buyInfo['out_num']);
            $user = getUserInfo($buyInfo['user_id'], 0);

//            $num = intval(usersMoney($buyInfo['user_id'], $config['money_id'], 1) / $buyInfo['price']); // 判断 当前余额 可以购买到多少股
//            if ($Pdnum > $num && $num > 0) {
//                $Pdnum = $num;
//            }
            $money = $Pdnum * $buyInfo['price']; // 按买入的价格计算
// 判断当前会员余额是否大于 本次要交易的总金额
//            if (usersMoney($buyInfo['user_id'], $config['money_id'], 1) >= $money) {
            if ($Pdnum >= 0) {
//                userMoneyAddLog($buyInfo['user_id'], $config['money_id'], '-' . $money, 0, 115, $sellInfo['price'] . '元/股，买入' . $Pdnum . '股' . $sellInfo['price'] . '*' . $Pdnum . '=' . $money);
                $buyStatu = editSharesBuyInfo($buyInfo, $Pdnum);

                $OutUser = getUserInfo($userId, 0);
                if ($config['buy_fee'] > 0) {
                    $poundage = $config['buy_fee'] * $Pdnum / 100; // 手续续折算数量
                    sharesLog($user['user_id'], $buyInfo['shares_id'], $Pdnum - $poundage, 0, 2, $buyInfo['price'] . '买入' . $Pdnum . '股手续费' . $config['buy_fee'] . '%' . '卖家：' . $OutUser['account'], '', $OutUser['user_id']);
                } else {
                    sharesLog($user['user_id'], $buyInfo['shares_id'], $Pdnum, 0, 2, $buyInfo['price'] . '买入,卖家：' . $OutUser['account'], '', $sellInfo['user_id']); // 添加买家股票变动数据
                }

//                usersDay($user['user_id'], 'out_' . $config['money_id'], $money, 0, 0, 0, 0); // 会员消费的费用
                sharesDay(0, 0, $Pdnum);
// 给卖家算钱
                $sellStatu = editSharesSellInfo($sellInfo, $Pdnum);

                unifiedOutPer($sellInfo['user_id'], $sellInfo['price'], $Pdnum, $money, $config, $buyInfo['user_id']); // 统一分配 卖出股票的金额

                sharesRiseLogAdd($sellInfo['shares_id'], $Pdnum); //  累计成交数量 做为股票按交易量涨价用


                sharesTradeAdd($sellInfo['id'], $sellInfo['user_id'], $buyInfo['id'], $buyInfo['user_id'], $config['money_id'], $config['shares_id'], $Pdnum, $sellInfo['price'], $config['buy_fee']);
//                # 发放股票交易奖
//                if ($user['tjr_id'] > 0) {
//                    bonus2Clear($user['tjr_id'], $user['user_id'], $Pdnum * $buyInfo['price'], $user['account'] . '买入' . $sharesInfo['name_cn'] . $Pdnum . $sharesInfo['thigh']);
//                }
//                # 发放交易佣金
//                if ($user['bdr_id'] > 0) {
//                    bonus8Clear($user['bdr_id'], $user['user_id'], $Pdnum * $buyInfo['price'], $user['account'] . '买入' . $sharesInfo['name_cn'] . $Pdnum . $sharesInfo['thigh']);
//                }
// 重复查询 本条数据是否还有没有完成的交易
                if ($sellStatu == 1) {
                    sharesOutPp($userId, $id, $config);
                }
            }
        }
    }
}

/**
 *  买入多单匹配交易
 * @param int $userId 会员id
 * @param int $id  买入数据的当条id
 * @param array $config  配置
 * @return bool
 */
function sharesAddPp($userId, $id, $config) {
//    set_time_limit(86400);
    $user = getUserInfo($userId, 0);
    $buyInfo = M(NUOYILIANNAME.'.shares_buy')->where(array('id' => $id, 'user_id' => $userId, 'pt' => PTVAL))->find(); // 当条购买数据记录
    $sharesInfo = getSharesInfo($buyInfo['shares_id']);
// 先看公司的 是否发行交易  如果发行 就优先成交公司内部股票
    $issueInfo = M(NUOYILIANNAME.'.shares_issue')->where(array('shares_id' => $buyInfo['shares_id'], 'issue_price' => $buyInfo['price'], 'is_type' => 1))->order('id asc')->find();
    if ($issueInfo && $issueInfo['is_type'] == 1) {
        $num = $issueInfo['issue_num'] - $issueInfo['out_num'];
        $Pdnum = min($buyInfo['out_num'], $num);
//        $money = $Pdnum * $buyInfo['price']; // 本次购买的价格
//        if (usersMoney($userId, $config['money_id'], 1) >= $money) {
        if ($Pdnum > 0) {
//            userMoneyAddLog($userId, $config['money_id'], '-' . $money, 0, 115, $buyInfo['price'] . '/个，买入' . $Pdnum . '公司原始股' . $buyInfo['price'] . '*' . $Pdnum . '=' . $money); // 发起扣款交易
//            usersDay($userId, 'out_' . $config['money_id'], $money, 0, 0, 0, 0);
            sharesIssueList($issueInfo['id'], $Pdnum); // 调交 并易数据
            sharesDay(0, 0, $Pdnum);

            sharesRiseLogAdd($buyInfo['shares_id'], $Pdnum); //  累计成交数量 做为股票按交易量涨价用
//            sharesPriceAutoChangeInfo($buyInfo['shares_id']); // 调用自动涨价数据

//            SharesAutoSplitInfo($buyInfo['shares_id']); // 调用 自动拆分数据

            $buyStatu = editSharesBuyInfo($buyInfo, $Pdnum);
            if ($config['buy_fee'] > 0) {
                $poundage = $config['buy_fee'] * $Pdnum / 100; // 手续续折算数量
                sharesLog($userId, $buyInfo['shares_id'], $Pdnum - $poundage, 0, 2, $buyInfo['price'] . '买入原始诺一链' . $Pdnum . '手续费' . $config['buy_fee'] . '%');
            } else {
                sharesLog($userId, $buyInfo['shares_id'], $Pdnum, 0, 2, $buyInfo['price'] . '公司原始诺一链');
            }

            sharesTradeAdd(0, 0, $buyInfo['id'], $buyInfo['user_id'], $config['money_id'], $config['shares_id'], $Pdnum, $buyInfo['price'], $config['buy_fee'], $issueInfo['id']);
//            # 发放股票交易奖
//            if ($user['tjr_id'] > 0) {
//                bonus2Clear($user['tjr_id'], $user['user_id'], $Pdnum * $buyInfo['price'], $user['account'] . '买入' . $sharesInfo['name_cn'] . $Pdnum . $sharesInfo['thigh']);
//            }
//
//            # 发放交易佣金
//            if ($user['bdr_id'] > 0) {
//                bonus8Clear($user['bdr_id'], $user['user_id'], $Pdnum * $buyInfo['price'], $user['account'] . '买入' . $sharesInfo['name_cn'] . $Pdnum . $sharesInfo['thigh']);
//            }
            if ($buyStatu == 1) {
                sharesAddPp($userId, $id, $config);
            }
        }
    } else {

// 如果公司的己出售完成 或者未开启交易 就查出其它会员出售的价格与本条要购买价格一致的数据
        $sellInfo = M(NUOYILIANNAME.'.shares_sell')->where(array('user_id' => array('neq', $userId), 'price' => $buyInfo['price'], 'status' => array('lt', 3), 'shares_id' => $buyInfo['shares_id'],'pt' => PTVAL))->order('zf_time asc')->limit('1')->find();
        $OutUser = getUserInfo($sellInfo['user_id'], 0);
        if ($sellInfo['out_num'] > 0 && $buyInfo['out_num'] > 0) {
            $Pdnum = min($buyInfo['out_num'], $sellInfo['out_num']);
            $money = $Pdnum * $buyInfo['price'];
// 判断当前会员余额是否大于 本次要交易的总金额
//            if (usersMoney($buyInfo['user_id'], $config['money_id'], 1) >= $money) {
            if ($Pdnum >= 0) {
//                userMoneyAddLog($buyInfo['user_id'], $config['money_id'], '-' . $money, 0, 115, $buyInfo['price'] . '/股，买入' . $Pdnum . '股' . $buyInfo['price'] . '*' . $Pdnum . '=' . $money); // 发起扣款交易

                if ($config['buy_fee'] > 0) {
                    $poundage = $config['buy_fee'] * $Pdnum / 100; // 手续续折算数量
                    sharesLog($buyInfo['user_id'], $buyInfo['shares_id'], $Pdnum - $poundage, 0, 2, $buyInfo['price'] . '买入' . $Pdnum . '个手续费' . $config['buy_fee'] . '%' . '卖家：' . $OutUser['account'], '', $OutUser['user_id']);
                } else {
                    sharesLog($buyInfo['user_id'], $buyInfo['shares_id'], $Pdnum, 0, 2, $buyInfo['price'] . '买入，卖家：' . $OutUser['account'], '', $sellInfo['user_id']); // 添加买家股票变动数据
                }
                sharesDay(0, 0, $Pdnum);
// 给卖家算钱
                $sellStatu = editSharesSellInfo($sellInfo, $Pdnum);
                sharesRiseLogAdd($sellInfo['shares_id'], $Pdnum); //  累计成交数量 做为股票按交易量涨价用
                unifiedOutPer($sellInfo['user_id'], $sellInfo['price'], $Pdnum, $money, $config, $buyInfo['user_id']); // 售出股票 统一分配 钱包
// 重复查询 本条数据是否还有没有完成的交易
                $buyStatu = editSharesBuyInfo($buyInfo, $Pdnum);
                sharesTradeAdd($sellInfo['id'], $sellInfo['user_id'], $buyInfo['id'], $buyInfo['user_id'], $config['money_id'], $config['shares_id'], $Pdnum, $sellInfo['price'], $config['buy_fee']);
                # 发放股票交易奖
//                if ($user['tjr_id'] > 0) {
//                    bonus2Clear($user['tjr_id'], $user['user_id'], $Pdnum * $buyInfo['price'], $user['account'] . '买入' . $sharesInfo['name_cn'] . $Pdnum . $sharesInfo['thigh']);
//                }
//                # 发放交易佣金
//                if ($user['bdr_id'] > 0) {
//                    bonus8Clear($user['bdr_id'], $user['user_id'], $Pdnum * $buyInfo['price'], $user['account'] . '买入' . $sharesInfo['name_cn'] . $Pdnum . $sharesInfo['thigh']);
//                }
                if ($buyStatu == 1) {
                    sharesAddPp($userId, $id, $config);
                }
            }
        }
    }
    return true;
}


/**
 *   卖出股票 统一计算 卖出后的分配方式
 * @param int $user_id   会员 id
 * @param float|int $price   单价
 * @param int $num   数量
 * @param int|float $money  卖出总金额
 * @param array $config   当条股票 交易规则
 * @param int $come_user_id   相关会员
 * @return bool
 */
function unifiedOutPer($user_id, $price, $num, $money, $config, $come_user_id) {
    $note = $price . '/诺一链，卖' . $num . '诺一链' . $num . '*' . $price . '=' . $money . "\n";
    if ($config['sell_fee'] > 0) {
        $note .= '手续费' . $config['sell_fee'] . '%';
        $sellFee = $money * $config['sell_fee'] / 100;
//        if ($sellFee > 0) {
//            userMoneyAddLog(1, 1, $sellFee, 0, 115, $note);
//        }
        $yjMoney = $money - $sellFee;
    } else {
        $yjMoney = $money;
    }
    # 获取会员卖出股票所得金额
    $sellSharesMoney = floatval(M('money_log')->where(array('user_id' => $user_id, 'is_type' => 115, 'money' => array('gt', 0)))->sum('money'));
    # 获取会员买入股票所花金额
    $buySharesMoney = abs(floatval(M('money_log')->where(array('user_id' => $user_id, 'is_type' => 115, 'money' => array('lt', 0)))->sum('money')));
    if($sellSharesMoney/$buySharesMoney >= 4) {
        if(($sellSharesMoney+$yjMoney)/$buySharesMoney > 5) {
            $yjMoney = $buySharesMoney*5-$sellSharesMoney;
        }
        userMoneyLogAdd($user_id, 9, $yjMoney, 115, $note);
        // userMoneyAddLog($user_id, 9, $yjMoney, 0, 115, $note, '');
    }else{
        if ($config['sell_per1'] > 0 && $config['sell_money1'] > 0) {
            $moneyP1 = $yjMoney * $config['sell_per1'] / 100;
            $note1 = $note . $yjMoney . '*' . $config['sell_per1'] . '%= ' . $moneyP1;
            userMoneyLogAdd($user_id, $config['sell_money1'], $moneyP1, 115, $note1);
            // userMoneyAddLog($user_id, $config['sell_money1'], $moneyP1, 0, 115, $note1, '');
        }
        if ($config['sell_per2'] > 0 && $config['sell_money2'] > 0) {
            $moneyP2 = $yjMoney * $config['sell_per2'] / 100;
            $note2 = $note . $yjMoney . '*' . $config['sell_per2'] . '%= ' . $moneyP2;
            userMoneyLogAdd($user_id, $config['sell_money2'], $moneyP2, 115, $note2);
            // userMoneyAddLog($user_id, $config['sell_money2'], $moneyP2, 0, 115, $note2, '');
        }
        if ($config['sell_per3'] > 0 && $config['sell_money3'] > 0) {
            $moneyP3 = $yjMoney * $config['sell_per3'] / 100;
            $note3 = $note . $yjMoney . '*' . $config['sell_per3'] . '%= ' . $moneyP3;
            userMoneyLogAdd($user_id, $config['sell_money3'], $moneyP3, 115, $note3);
            // userMoneyAddLog($user_id, $config['sell_money3'], $moneyP3, 0, 115, $note3, '');
        }
        if ($config['sell_per4'] > 0 && $config['sell_money4'] > 0) {
            $moneyP4 = $yjMoney * $config['sell_per4'] / 100;
            $note4 = $note . $yjMoney . '*' . $config['sell_per4'] . '%= ' . $moneyP4;
            userMoneyLogAdd($user_id, $config['sell_money4'], $moneyP4, 115, $note4);
            // userMoneyAddLog($user_id, $config['sell_money4'], $moneyP4, 0, 115, $note4, '');
        }
        if ($config['sell_per5'] > 0 && $config['sell_money5'] > 0) {
            $moneyP5 = $yjMoney * $config['sell_per5'] / 100;
            $note5 = $note . $yjMoney . '*' . $config['sell_per5'] . '%= ' . $moneyP5;
            userMoneyLogAdd($user_id, $config['sell_money5'], $moneyP5, 115, $note5);
            // userMoneyAddLog($user_id, $config['sell_money5'], $moneyP5, 0, 115, $note5, '');
        }
    }
    if(($sellSharesMoney+$yjMoney)/$buySharesMoney >= 5) {
        # 会员待买入的记录
        $userBuySharesList = M(NUOYILIANNAME.'.shares_buy')->where(array('user_id' => $user_id, 'status' => array('in', array(1,2)),'pt' => PTVAL))->select();
        foreach($userBuySharesList as $v) {
            if($v['pt'] == PTVAL) {
                userMoneyLogAdd($user_id, $v['money_id'], $v['out_num']*$v['price'], 119, '收入达到5倍自动撤回');
            } else {
                userMoneyAddLogForOne($user_id, $v['money_id'], $v['out_num']*$v['price'], 0, 118, '收入达到5倍自动撤回', '');
            }
            $data = array(
                'return_num' => $v['out_num'],
                'return_time' => time(),
                'out_num' => 0,
                'status' => 3
            );
            M(NUOYILIANNAME.'.shares_buy')->where(array('id' => $v['id']))->save($data);
        }
        # 会员待卖出记录
        $userSellSharesList = M(NUOYILIANNAME.'.shares_sell')->where(array('user_id' => $user_id, 'status' => array('in', array(1,2)),'pt' => PTVAL))->select();
        foreach($userSellSharesList as $v) {
            sharesLog($user_id, 1, $v['out_num'], 0, 9, '收入达到5倍自动撤回', '', $user_id); // 添加买家股票变动数据
            $data = array(
                'return_num' => $v['out_num'],
                'return_time' => time(),
                'out_num' => 0,
                'status' => 3
            );
            M(NUOYILIANNAME.'.shares_sell')->where(array('id' => $v['id']))->save($data);
        }
        sharesLog($user_id, 1, 0, usersShares($user_id,1)['money'], 5, '收入达到5倍', '', $user_id); // 添加买家股票变动数据
    }
    return TRUE;
}

/**
 * 修改 股票卖家 数据
 * @param type $config 当条数据 数组
 * @param type $pdNum 本次交易数量   1  还要继续交易   9 交易成功了
 */
function editSharesSellInfo($config, $pdNum) {
    $sellOutNum = $config['out_num'] - $pdNum;
    if ($sellOutNum <= 0) {
        M(NUOYILIANNAME.'.shares_sell')->where(array('id' => $config['id']))->save(array('status' => 9, 'out_time' => time(), 'out_num' => $sellOutNum));
        return 9;
    } else {
        if ($config['status'] == 1) {
            M(NUOYILIANNAME.'.shares_sell')->where(array('id' => $config['id']))->save(array('status' => 2, 'out_num' => $sellOutNum));
        } else {
            M(NUOYILIANNAME.'.shares_sell')->where(array('id' => $config['id']))->save(array('out_num' => $sellOutNum));
        }
        return 1;
    }
}

/**
 * 添加股票的交易数据
 * @param type $id
 * @param type $price
 */
function sharesTradeAdd($sellId, $sellUid, $buyId, $buyUid, $moneyId, $sharesId, $num, $price, $poundage, $issue = '') {
    $data['sell_id'] = $sellId ? $sellId : FALSE;
    $data['sell_uid'] = $sellUid ? $sellUid : FALSE;
    $data['buy_id'] = $buyId; //买家id
    $data['buy_uid'] = $buyUid; //买家会员id
    $data['money_id'] = $moneyId; // 钱包id
    $data['shares_id'] = $sharesId; // 钱包id
    $data['num'] = $num; // 交易数量
    $data['price'] = $price; // 交易价格
    $data['money'] = $num * $price; // 数量 * 价格 =  总价
    $data['poundage'] = $poundage; // 手续费比例
    $data['poundage_num'] = $poundage * $num / 100; // 手续续折算数量
    $data['add_time'] = time(); // 待付款
    $data['issue'] = $issue ? $issue : FALSE;
    $data['pt'] = M(NUOYILIANNAME.".shares_buy")->where(array('id' => $buyId))->getField('pt');
    $res = M(NUOYILIANNAME.'.shares_trade')->add($data);
    if ($res) {
        return $res;
    } else {
        return FALSE;
    }
}
/**
 * 修改 股票买家 数据
 * @param type $config 当条数据 数组
 * @param type $pdNum 本次交易数量  1  还要继续交易   9 交易成功了
 */
function editSharesBuyInfo($config, $pdNum) {
    $sellOutNum = $config['out_num'] - $pdNum;
    if ($sellOutNum <= 0) {
        M(NUOYILIANNAME.'.shares_buy')->where(array('id' => $config['id']))->save(array('status' => 9, 'out_time' => time(), 'out_num' => $sellOutNum));
        return 9;
    } else {
        if ($config['status'] == 1) {
            M(NUOYILIANNAME.'.shares_buy')->where(array('id' => $config['id']))->save(array('status' => 2, 'out_num' => $sellOutNum));
        } else {
            M(NUOYILIANNAME.'.shares_buy')->where(array('id' => $config['id']))->save(array('out_num' => $sellOutNum));
        }
        return 1;
    }
}

/**
 * 查询股票数据
 * @param int $id 股票id
 * @return array|bool
 */
function getSharesInfo($id = 1) {
    $info = M(NUOYILIANNAME.'.shares')->where(array('id' => $id))->find();
    if ($info) {
        return $info;
    } else {
        return 0;
    }
}

/**
 * 公司发行股票数量交易  判段是否存在发行的交易数据
 * @param type $id 发行id
 * @param type $out_num  交易数量
 */
function sharesIssueList($id, $out_num) {
    $res = M(NUOYILIANNAME.'.shares_issue')->where(array('id' => $id))->setInc('out_num', $out_num);
    if ($res) {
        $info = M(NUOYILIANNAME.'.shares_issue')->where(array('id' => $id))->find();
        M(NUOYILIANNAME.'.shares')->where(array('id' => $info['shares_id']))->setInc('total_num', $out_num);
        if ($info['issue_num'] <= $info['out_num']) {
            M(NUOYILIANNAME.'.shares_issue')->where(array('id' => $id))->save(array('is_type' => 9, 'out_time' => time()));
        }
    }
}

/**
 * 股票走势
 * @param type $add 买入数量
 * @param type $adprice 买入价格
 * @param type $out 卖出量
 * @param type $outprice 卖出价格
 * @return boolean
 */
function sharesDay($add = 0, $out = 0, $total = 0) {
    set_time_limit(86400);
    $data['time'] = strtotime(date('Y-m-d'));
    $info = M(NUOYILIANNAME.'.shares_day')->where($data)->find();
    if (!$info) {
        M(NUOYILIANNAME.'.shares_day')->add($data);
    }
    $addnum = $info['add_num'] == '' ? 0 : $info['add_num'] + $add;
    $total_add_num = $info['total_add_num'] == '' ? 0 : $info['total_add_num'] + $add;
    $outnum = $info['out_num'] == '' ? 0 : $info['out_num'] + $out;
    $total_out_num = $info['total_out_num'] == '' ? 0 : $info['total_out_num'] + $out;
    $cj_num = $info['cj_num'] == '' ? 0 : $info['cj_num'] + $total;
    $sql = "UPDATE ".NUOYILIANNAME.".__PREFIX__shares_day SET add_num = add_num + $addnum,total_add_num = total_add_num + $total_add_num,out_num = out_num + $outnum, total_out_num = total_out_num + $total_out_num, cj_num = cj_num + $cj_num  WHERE time = '" . strtotime(date('Y-m-d')) . "'";
    D(NUOYILIANNAME.'.shares_day')->execute($sql);
    return true;
}

/**
 * 按交易量涨价  统计 上一次涨价后   成交了多少量
 * @param type $id
 * @param type $num
 */
function sharesRiseLogAdd($id = 1, $num) {
    if (M(NUOYILIANNAME.'.shares_rise_log')->where(array('shares_id' => $id, 'is_type' => 2))->find()) {
        M(NUOYILIANNAME.'.shares_rise_log')->where(array('shares_id' => $id, 'is_type' => 2))->setInc('buy_num', $num);
        sharesPriceAutoChangeInfo($id); // 提交自动涨价
    } else {
        $data['shares_id'] = $id;
        $data['buy_num'] = $num;
        $data['is_type'] = 2;
        $res = M(NUOYILIANNAME.'.shares_rise_log')->add($data);
    }
}



/**
 * 自动涨价管理
 */
function sharesPriceAutoChangeInfo($shares_id) {
    set_time_limit(86400);
    $info = M(NUOYILIANNAME.'.shares')->where(array('id' => $shares_id))->find();
// 开启自动涨价
    if ($info['rise_auto'] == 1) {
        $list = M(NUOYILIANNAME.'.shares_rise')->where(array('shares_id' => $info['id']))->find(); // 查找涨价规则
        if ($list['rise_type'] == 1) {
// 按发行数量涨价
            if ($info['total_num'] >= $list['trade_num']) {
                sharesPriceChangeInfo($shares_id, $info['now_price'] + $list['rose_price'], 1, ''); // 执行一条 股票价格变动记录
            }
        } else if ($list['rise_type'] == 2) {
// 否则是按周期 涨价的
            $priceList = M(NUOYILIANNAME.'.shares_price')->where(array('shares_id' => $shares_id, 'is_type' => 2))->order('id desc')->find(); // 查找涨价记录  按 固定涨价周期的最后一条
//            if (($priceList['zf_time'] + $list['cycle_num'] * 86400) < time()) {
//                sharesPriceChangeInfo($shares_id, $info['now_price'] + $list['rose_price'], 2, ''); // 执行一条 股票价格变动记录
//            }
        } else if ($list['rise_type'] == 3) {
// 按交易量涨价
            $buyNum = M(NUOYILIANNAME.'.shares_rise_log')->where(array('shares_id' => $shares_id, 'is_type' => 2))->find(); // 查找涨价记录  按 固定涨价周期的最后一条
            if ($buyNum['buy_num'] >= $list['out_num']) {
                if ($buyNum['buy_num'] >= $list['out_num']) {
                    $data['shares_id'] = $shares_id;
                    $data['buy_num'] = $buyNum['buy_num'] - $list['out_num'];
                    $data['is_type'] = 2;
                    $res = M(NUOYILIANNAME.'.shares_rise_log')->add($data);
                }
                sharesPriceChangeInfo($shares_id, $info['now_price'] + $list['rose_price'], 6, ''); // 执行一条 股票价格变动记录
                M(NUOYILIANNAME.'.shares_rise_log')->where(array('id' => $buyNum['id']))->save(array('is_type' => 1, 'zf_time' => time()));
            }
        }
    }
    return TRUE;
}


/**
 * 手动变更股票的当前交易价格
 * @param int $sharesId  股票id
 * @param int|float $after_price 修改后的价格
 * @param int $is_type 变动类型
 * @param string $note 备注说明
 * @return bool
 */
function sharesPriceChangeInfo($sharesId = 1, $after_price, $is_type, $note = '') {
//    set_time_limit(86400);
    $info = M(NUOYILIANNAME.'.shares')->where(array('id' => $sharesId))->field('now_price')->find();
    if ($info) {
        $data = array(
            'shares_id' => $sharesId,
            'front_price' => $info['now_price'],
            'after_price' => $after_price,
            'zf_time' => time(),
            'is_type' => $is_type,
            'note' => $note
        );
//        $data['shares_id'] = 1;
//        $data['front_price'] = $info['now_price'];
//        $data['after_price'] = $after_price;
//        $data['zf_time'] = time();
//        $data['is_type'] = $is_type;
//        $data['note'] = $note;
        $res = M(NUOYILIANNAME.'.shares_price')->add($data);
        M(NUOYILIANNAME.'.shares')->where(array('id' => $sharesId))->save(array('now_price' => $after_price));
        #  股票价格变动的情况下 统一修改 会员可购买量
//        unifiedBuyShanes($sharesId, $after_price);
        if ($res) {
            return $res;
        } else {
            return FALSE;
        }
    }
}


/**
 * 发行股票
 * @param array $post 提交的数据
 * @return array
 */
function sharesIssueAdd($post) {
//    set_time_limit(86400);
    $post['shares_id'] = intval($post['shares_id']);
    $post['issueNum'] = floatval($post['issueNum']);
    $post['issuePrice'] = floatval($post['issuePrice']);
    $shares = getSharesInfo($post['shares_id']);
    if ($post['issuePrice'] <= 0) {
        return array('status' => -1, 'msg' => '请输入发行价格');
    }
    if ($post['shares_id'] <= 0) {
        return array('status' => -1, 'msg' => '请选择要发行的股票');
    }
//    if ($post['issuePrice'] > $shares['max_out_price']) {
//        return array('status' => -1, 'msg' => '不能超出最高价格' . $shares['max_out_price']);
//    }
    if ($shares['total'] - $shares['out_num'] < $post['issueNum']) {
        return array('status' => -1, 'msg' => '当前最多可发行' . ($shares['total'] - $shares['out_num']));
    } else {
        $num = M(NUOYILIANNAME.'.shares_issue')->where(array('shares_id' => $post['shares_id']))->count();
        $model = M();
        $model->startTrans();
        $data = array(
            'shares_id' => $post['shares_id'],
            'issue_num' => $post['issueNum'],
            'issue_price' => $post['issuePrice'],
            'issue_stage' => $num + 1,
            'zf_time' => time(),
            'note' => $post['note'],
            'is_type' => 1,
            'out_num' => 0
        );
//        $data['shares_id'] = $post['shares_id'];
//        $data['issue_num'] = $post['issueNum'];
//        $data['issue_price'] = $post['issuePrice'];
//        $data['issue_stage'] = $num + 1;
//        $data['zf_time'] = time();
//        $data['note'] = $post['note'];
//        $data['is_type'] = 1;
//        $data['out_num'] = 0;
        $res = M(NUOYILIANNAME.'.shares_issue')->add($data);
        $info = M(NUOYILIANNAME.'.shares')->where(array('id' => $post['shares_id']))->setInc('out_num', $post['issueNum']);
        if ($res && $info) {
            sharesIssueBuy($res);
            $model->commit();
            return array('status' => 1, 'msg' => '发行成功');
        } else {
            $model->rollback();
            return array('status' => -1, 'msg' => '发行失败');
        }
    }
}


/**
 * 公司发行股票 多单交易  查看 会员的买家
 * @param type $id
 */
function sharesIssueBuy($id) {
//    set_time_limit(86400);
    $issueInfo = M(NUOYILIANNAME.'.shares_issue')->where(array('id' => $id, 'is_type' => 1))->order('id asc')->find();
    $sharesInfo = getSharesInfo($issueInfo['shares_id']);
//    $list = M('shares_buy')->where(array('shares_id' => $issueInfo['shares_id'], 'price' => $issueInfo['issue_price'], 'status' => array('lt', 3)))->order('sort desc')->limit('1')->find(); // 当条购买数据记录
    if (zfCache('securityInfo.gupiao_rc') == 1) {
        $buyInfo = M(NUOYILIANNAME.'.shares_buy')->where(array('price' => $issueInfo['issue_price'], 'status' => array('lt', 3), 'shares_id' => $issueInfo['shares_id'], 'zf_time' => array('elt', time() - zfCache('securityInfo.gupiao_rc_time') * 86400)))->order('sort desc')->limit('1')->find();
    } else {
        $buyInfo = M(NUOYILIANNAME.'.shares_buy')->where(array('price' => $issueInfo['issue_price'], 'status' => array('lt', 3), 'shares_id' => $issueInfo['shares_id']))->order('zf_time asc')->limit('1')->find();
    }
    if ($issueInfo && $buyInfo) {
        $config = M(NUOYILIANNAME.'.shares_config')->where(array('shares_id' => $buyInfo['shares_id'], 'is_type' => 1))->find();
        $issueNum = $issueInfo['issue_num'] - $issueInfo['out_num'];
        $Pdnum = min($issueNum, $buyInfo['out_num']);
//        $money = $Pdnum * $list['price']; // 本次购买的价格
//        if (usersMoney($list['user_id'], $config['money_id'], 1) >= $money) {
        if ($Pdnum > 0) {
//            userMoneyAddLog($list['user_id'], $config['money_id'], '-' . $money, 0, 115, $list['price'] . '元/股，买入' . $Pdnum . '股'); // 发起扣款交易
            $buyStatu = editSharesBuyInfo($buyInfo, $Pdnum);
            if($buyInfo['pt'] == PTVAL) {
                sharesLog($buyInfo['user_id'], $buyInfo['shares_id'], $Pdnum, 0, 2, $buyInfo['price'] . '买入公司原始股');
            } else {
                sharesLogForNext($buyInfo['user_id'], $buyInfo['shares_id'], $Pdnum, 0, 2, $buyInfo['price'] . '买入公司原始股');
            }
//            usersDay($list['user_id'], 'out_' . $config['money_id'], $money, 0, 0, 0, 0);
            sharesIssueList($issueInfo['id'], $Pdnum); // 调交 并易数据
            sharesDay(0, 0, $Pdnum);

            sharesPriceAutoChangeInfo($buyInfo['shares_id']); // 调用自动涨价数据
            SharesAutoSplitInfo($buyInfo['shares_id']); // 调用 自动拆分数据
            sharesRiseLogAdd($buyInfo['shares_id'], $Pdnum); //  累计成交数量 做为股票按交易量涨价用

            sharesTradeAdd(0, 0, $buyInfo['id'], $buyInfo['user_id'], $config['money_id'], $config['shares_id'], $Pdnum, $buyInfo['price'], $config['buy_fee'], $issueInfo['id']);

            // $user = getUserInfo($buyInfo['user_id']);

            $out = M(NUOYILIANNAME.'.shares_issue')->where(array('shares_id' => $id, 'is_type' => 1))->order('id asc')->find();
            if ($out['issue_num'] > $out['out_num']) {
//                sleep(1); // 让程序 停止一秒后在执行
                sharesIssueBuy($id);
            } else {
                return TRUE;
            }
        }
    } else {
        return TRUE;
    }
}



/**
 * 自动拆分管理
 */
function SharesAutoSplitInfo($shares_id) {
//    set_time_limit(86400);
    $info = M(NUOYILIANNAME.'.shares')->where(array('id' => $shares_id))->find();
    // 开启自动拆分 并且当价交易价格 大于等于 拆分价格 执行自动拆分
    if ($info['split_auto'] == 1 && $info['now_price'] >= $info['split_price']) {
        $list = M(NUOYILIANNAME.'.shares_user')->where(array('is_type' => 1, 'shares_id' => $shares_id))->select();
        $model = new \Think\Model();
        $model->startTrans();
        if ($list) {
            $data['shares_id'] = $shares_id;
            $data['add_total'] = D('shares_user')->where(array('shares_id' => $shares_id))->sum('money');
            $data['out_total'] = $data['add_total'] * $info['split_per'];
            $data['per'] = $info['split_per'];
            $data['add_price'] = $info['now_price'];
            $data['out_price'] = $info['split_fall_price'];
            $data['user_total'] = D('shares_user')->where(array('shares_id' => $shares_id))->count();
            $data['zf_time'] = time();
            $data['note'] = '自动执行拆分';
            $res = D(NUOYILIANNAME.'.shares_split')->add($data);
            foreach ($list as $v) {
                if($v['pt'] == PTVAL) {
                    $resb = sharesLog($v['user_id'], $shares_id, $v['money'] * $info['split_per'] - $v['money'], 0, 5, '自动拆分' . $info['split_per'] . '倍');
                } else {
                    $resb = sharesLogForNext($v['user_id'], $shares_id, $v['money'] * $info['split_per'] - $v['money'], 0, 5, '自动拆分' . $info['split_per'] . '倍');
                }
                M(NUOYILIANNAME.'.shares_user')->where(array('id' => $v['id']))->setInc('split_cs', 1); //  加1 次
            }
            $resc = M(NUOYILIANNAME.'.shares')->where(array('id' => $shares_id))->setInc('split_num', 1); // 自动拆分 加1 次
        }
        $resd = sharesPriceChangeInfo($shares_id, $info['split_fall_price'], 4); // 调用修改股票的交易价格变动方法
        if ($res && $resb && $resc && $resd) {
            $model->commit();
            return array('status' => 1, 'msg' => '操作成功');
        } else {
            $model->rollback();
            return array('status' => -1, 'msg' => '操作失败');
        }
    }
}


/**
 * 对会员股票做修改
 * @param array $post 提交的数据
 * @return array 操作结果
 */
function handSharesUser($post) {
    $user = getUserInfo($post['account'], 3);
    if ($user) {
        # 获取平台第一个会员
//        $firstUser = M('users')->order('user_id asc')->field('user_id, account')->find();
//        if ($firstUser['user_id'] == $user['user_id']) {
//            return array('status' => -1, 'msg' => '不能操作' . $firstUser['account']);
//        }
        $total = M(NUOYILIANNAME.'.shares_user')->where(array('user_id' => $user['user_id'], 'shares_id' => $post['shares_id'], 'pt' => PTVAL))->find();
        if ($post['is_type'] == 1) {
            $res = sharesLog($user['user_id'], $post['shares_id'], $post['money'], 0, 7, $post['note'], $_SESSION['admin_id']);
        }
        if ($post['is_type'] == 2) {
            if ($total['money'] < $post['money']) {
                return array('status' => -1, 'msg' => '数量不足!');
            }
            $res = sharesLog($user['user_id'], $post['shares_id'], '-' . $post['money'], 0, 7, $post['note'], $_SESSION['admin_id']);
//            $res = sharesLog($firstUser['user_id'], $post['shares_id'], $post['money'], 0, 7, $post['note'] . $user['account'], $_SESSION['admin_id']);
        }
        if ($post['is_type'] == 3) {
            if ($total['money'] < $post['money']) {
                return array('status' => -1, 'msg' => '冻结数量大于可用数量，请重新输入');
            }
            $res = sharesLog($user['user_id'], $post['shares_id'], 0, $post['money'], 5, $post['note'], $_SESSION['admin_id']);
        }
        if ($post['is_type'] == 4) {
            if ($total['frozen'] < $post['money']) {
                return array('status' => -1, 'msg' => '释放数量大于冻结数量，请重新输入');
            }
            $res = sharesLog($user['user_id'], $post['shares_id'], 0, '-' . $post['money'], 6, $post['note'], $_SESSION['admin_id']);
        }
        if ($res) {
            return array('status' => 1, 'msg' => '操作成功');
        }
    } else {
        return array('status' => -1, 'msg' => '此会员不存在');
    }
}
/**
 * 买入限制
 */
function xz(){
    $shares_rise = M(NUOYILIANNAME.'.shares_rise')->where(array('is_type' => 1))->find();
    $shares = M(NUOYILIANNAME.'.shares')->field('now_price')->find();
    $shares_buy = M(NUOYILIANNAME.'.shares_buy')->where(array('price' => $shares['now_price']))->sum('num');
    $sj = floatval($shares_rise['out_num']) - floatval($shares_buy);
    return $sj;
}
/**
 * 卖出现在
 */
function mcxz(){
    $shares_rise = M(NUOYILIANNAME.'.shares_rise')->where(array('is_type' => 1))->find();
    $shares = M(NUOYILIANNAME.'.shares')->field('now_price')->find();
    $shares_buy = M(NUOYILIANNAME.'.shares_sell')->where(array('price' => $shares['now_price']))->sum('num');
    $sj = floatval($shares_rise['out_num']) - floatval($shares_buy);
    return $sj;
}