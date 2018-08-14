<?php

/**
 * 新注册的会员 统一添加地址
 * @param type $userId
 */
function userBlockAdd($userId) {
    $list = M('block')->field('id')->select();
    if ($list) {
        foreach ($list as $v) {
            $data['uid'] = $userId;
            $data['bid'] = $v['id'];
            $data['address'] = random_str(32);
            M('block_user')->add($data);
        }
    }
    return TRUE;
}

/**
 * 调用钱包图片
 * @param type $mId  钱包 id
 * @return type
 */
function blockOneImg($bId) {
    return M('block')->where(array('id' => $bId))->cache('blockOneImg' . $bId)->getField('logo');
}

function blockTradeStatus($id)
{
    $data = array();
    $data[1] = '待打款';
    $data[2] = '待收款';
    $data[3] = '超时打款';
    $data[4] = '超时收款';
    $data[5] = '投诉';
  	$data[6] = '撤回';
    $data[9] = '已完成';
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }

}

function blockSellStatus($id)
{
    $data = array();
    $data[1] = '待交易';
    $data[2] = '交易中';
    $data[3] = '撤销';
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

function blockrecoveryStatus($id) {
    $data = array();
    $data[1] = "未处理";
    $data[2] = "已处理";
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

/**
 * 货币信息
 * @return type array
 */
function blockInfo($id) {
    return $info = M('block')->where(array('id' => $id))->find();
}

/**
 * 参数管理
 * @param int $moneyId  钱包 id
 * @param int $a 类型
 * @return float|int
 */
function blockList($bId = 1, $a = 1) {
    $info = M('block')->where(array('id' => $bId))->find();
    if ($a == 1) {
        return $info['name_cn'];
    }
    if ($a == 2) {
        return $info['now_price'];
    }
    if ($a == 3) {
        return $info['logo'];
    }
    if ($a == 4) {
        return $info['name_en'];
    }
}

/**
 * 查询会员可用余额
 * @param int $uId 会员 ID
 * @param int $mId 钱包 ID
 * @param int $a   1 查询可用余额   2  查询冻结金额 100 所有钱包总和 3 查询 可有余我
 * @return float|int 金额
 */
function usersBlock($uId, $bId = 1, $a = 1) {
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
        if ($a == 4) {
            return $info['address'];
        }
        if ($a == 5) {
            return $info['deposit'];
        }
    } else {
        return 0;
    }
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
function userBlockLogAdd($uId, $bId = 1, $money = 0, $type, $note = '', $adminId = '', $comeUid = '', $order_id = '') {
    $data['uid'] = $uId;
    $data['bid'] = $bId;
    $info = M('block_user')->where($data)->find();
    if (!$info) {
        $data['address'] = random_str(32);
        M('block_user')->add($data);
    }
    $acc['uid'] = $uId;
    $acc['bid'] = $bId;
    $acc['money'] = $money;
    $acc['is_type'] = $type;
    $acc['zf_time'] = time();
    $acc['per'] = blockList(1, 2);
    $note && $acc['note'] = $note;
    $adminId && $acc['admin_id'] = $adminId;
    $comeUid && $acc['come_uid'] = $comeUid;
    $order_id && $acc['order_id'] = $order_id;	
    $sql = "UPDATE __PREFIX__block_user SET money = money + $money  WHERE uid = $uId and bid = $bId";
    if (D('block_user')->execute($sql)) {
        $acc['total'] = M('block_user')->where(array('uid' => $uId, 'bid' => $bId))->getField('money');
        return M('block_log')->add($acc);
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
function lockUserBlockAdd($uId, $Locknum, $bId = 1, $type = 1, $note = '') {
    $data['uid'] = $uId;
    $data['bid'] = $bId;
    $data['lock_time'] = time();
    $data['frozen'] = $Locknum;
    $data['statu'] = 2;
    $data['type'] = $type;
    $note && $data['note'] = $note;
    if (M('block_user')->where(array('bid' => $bId, 'uid' => $uId))->setInc('frozen', $Locknum)) {
        return M('block_user_lock')->add($data);
    }
}

function lockUserBlockStatu($id) {
    $data = array();
//    $data[1] = "申请担保人";
    $data[3] = "公司赠送冻结";
    $data[4] = "管理员调冻结";
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

function blockTransformStatu($id) {
    $data = array();
    $data[1] = "待审核";
    $data[2] = "己审核";
    $data[3] = "己拒绝";
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

function blockcrowdStatus($id) {
    $data = array();
    $data[1] = "准备中";
    $data[2] = "进行中";
    $data[3] = "己结束";
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

function blockLogType($id) {
    $data = array();
    $data[1] = "买入";
    $data[2] = "储存奖";
    $data[3] = "订单支付";
    $data[4] = "撤单";
    $data[7] = "管理员操作";
    $data[8] = "储存释放";
    $data[9] = "储存YML转出";
    $data[10] = "积分转出";
    $data[105] = "转账";
//    $data[106] = "存宝日息";
//    $data[107] = "存宝团队日息";
    $data[108] = "积分兑换";
//    $data[109] = "矿区块仓储";
    $data[110] = "储存";
  	$data[111] = "YML买入";
    $data[112] = "卖出撤回";
    $data[113] = "超时未打款撤回";
    $data[114] = "超时未收款撤回";
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

/**
 * @return float|int  浮动
 */
function optimumBuy() {
    $block = M('block')->where(array('id' => 1))->field('now_price,float_price')->find();
    $float = $block['now_price'] * $block['float_price'] / 100;
    $price = $block['now_price'] - $float;
    return $price;
}

function optimumSell() {
    $block = M('block')->where(array('id' => 1))->field('now_price,float_price')->find();
    $float = $block['now_price'] * $block['float_price'] / 100;
    $price = $block['now_price'] + $float;
    return $price;
}

/**
 * 最大买入
 */
function BuyMoney($userId) {
    $block = M('block')->where(array('id' => 1))->field('buy_mid')->find();
    $maxBuy = usersMoney($userId, $block['buy_mid']);
    return $maxBuy;
}

function SellMoney($userId) {
    $maxBuy = usersMoney($userId, 2);
    return $maxBuy;
}

/**
 *  买入多单匹配交易
 * @param int $user_id 会员id
 * @param int $id  买入数据的当条id
 * @return bool
 */
function blockAddPp($id = 0) {
    if ($id > 0) {
        $buyInfo = M('block_buy')->where(array('id' => $id))->find();
    } else {
        $buyInfo = M('block_buy')->where(array('status' => array('lt', 3)))->order('id asc')->find();
    }
    if (!$buyInfo) {
        return true;
    }
    // 查出 卖家数据  价格等于 买入价格一样的第一条数据
    $sellInfo = M('block_sell')->where(array('uid' => array('neq', $buyInfo['uid']), 'price' => $buyInfo['price'], 'bid' => $buyInfo['bid'], 'status' => array('lt', 3), 'stay_num' => array('gt', 0)))->order('add_time desc')->limit('1')->find();
    // 如果待购买数量  与 待卖出数量都大于 0 才进去
    if ($buyInfo['stay_num'] > 0 && $sellInfo['stay_num'] > 0) {
        $Pdnum = min($buyInfo['stay_num'], $sellInfo['stay_num']); // 待买与待卖数量取最小的值
        if ($buyInfo['stay_num'] >= $Pdnum && $sellInfo['stay_num'] >= $Pdnum) {
            $model = new \Think\Model();
            $model->startTrans();
            $infoa = editBlockBuyInfo($buyInfo, $Pdnum);
            $infob = editBlockSellInfo($sellInfo, $Pdnum);
            $infoc = blockPdAdd($sellInfo['id'], $sellInfo['uid'], $buyInfo['id'], $buyInfo['uid'], $buyInfo['bid'], $Pdnum, $buyInfo['price'], $buyInfo['poundage']);
            $infod = userBlockLogAdd($buyInfo['uid'], $buyInfo['bid'], $Pdnum, 1, '买入', '', $sellInfo['uid']);
            $infoe = unifiedOutPer($sellInfo['uid'], $sellInfo['price'], $Pdnum, $buyInfo['bid'], $buyInfo['uid']);
            if ($infoa && $infob && $infoc && $infod && $infoe) {
                $model->commit();
            } else {
                $model->rollback();
            }
            blockAddPp($id);
        }
    } else {
        return true;
    }
}

/**
 *  卖出多单匹配交易
 * @param int $user_id 会员id
 * @param int $id  买入数据的当条id
 * @return bool
 */
function blockOutPp($id = 0) {
    if ($id > 0) {
        $sellInfo = M('block_sell')->where(array('id' => $id))->find();
    } else {
        $sellInfo = M('block_sell')->where(array('status' => array('lt', 3)))->order('id asc')->find();
    }
    if (!$sellInfo) {
        return true;
    }
    $buyInfo = M('block_buy')->where(array('uid' => array('neq', $sellInfo['uid']), 'price' => $sellInfo['price'], 'bid' => $sellInfo['bid'], 'status' => array('lt', 3), 'stay_num' => array('gt', 0)))->order('add_time desc')->limit('1')->find();
    if ($sellInfo['stay_num'] > 0 && $buyInfo['stay_num'] > 0) {
        $Pdnum = min($sellInfo['stay_num'], $buyInfo['stay_num']);
        if ($buyInfo['stay_num'] >= $Pdnum && $sellInfo['stay_num'] >= $Pdnum) {
            $model = new \Think\Model();
            $model->startTrans();
            $infoa = editBlockBuyInfo($buyInfo, $Pdnum);
            $infob = editBlockSellInfo($sellInfo, $Pdnum);
            $infoc = blockPdAdd($sellInfo['id'], $sellInfo['uid'], $buyInfo['id'], $buyInfo['uid'], $buyInfo['bid'], $Pdnum, $sellInfo['price'], $buyInfo['poundage']);
            $infod = userBlockLogAdd($buyInfo['uid'], $buyInfo['bid'], $Pdnum, 1, '买入', '', $sellInfo['uid']);
            $infoe = unifiedOutPer($sellInfo['uid'], $sellInfo['price'], $Pdnum, $buyInfo['bid'], $buyInfo['uid']);
            if ($infoa && $infob && $infoc && $infod && $infoe) {
                $model->commit();
            } else {
                $model->rollback();
            }
        }
    } else {
        return true;
    }
}

/**
 * 修改 股票买家 数据
 * @param type $config 当条数据 数组
 * @param type $pdNum 本次交易数量
 */
function editBlockBuyInfo($config, $pdNum) {
    $sellOutNum = $config['stay_num'] - $pdNum;
    if ($sellOutNum <= 0) {
        M('block_buy')->where(array('id' => $config['id']))->save(array('status' => 9, 'out_time' => time(), 'stay_num' => $sellOutNum, 'zf_num' => $pdNum, 'zf_time' => time()));
        return 9;
    } else {
        if ($config['status'] == 1) {
            M('block_buy')->where(array('id' => $config['id']))->save(array('status' => 2, 'stay_num' => $sellOutNum, 'zf_num' => $pdNum, 'zf_time' => time()));
        } else {
            M('block_buy')->where(array('id' => $config['id']))->save(array('stay_num' => $sellOutNum, 'zf_num' => $pdNum, 'zf_time' => time()));
        }
        return 1;
    }
}

/**
 * 修改 股票卖家 数据
 * @param type $config 当条数据 数组
 * @param type $pdNum 本次交易数量
 */
function editBlockSellInfo($config, $pdNum) {
    $sellOutNum = $config['stay_num'] - $pdNum;
    if ($sellOutNum <= 0) {
        M('block_sell')->where(array('id' => $config['id']))->save(array('status' => 9, 'out_time' => time(), 'stay_num' => $sellOutNum, 'zf_num' => $pdNum, 'zf_time' => time()));
        return 9;
    } else {
        if ($config['status'] == 1) {
            M('block_sell')->where(array('id' => $config['id']))->save(array('status' => 2, 'stay_num' => $sellOutNum, 'zf_num' => $pdNum, 'zf_time' => time()));
        } else {
            M('block_sell')->where(array('id' => $config['id']))->save(array('stay_num' => $sellOutNum, 'zf_num' => $pdNum, 'zf_time' => time()));
        }
        return 1;
    }
}

/**
 * 添加交易数据表
 * @param type $sell_id 卖家ID
 * @param type $buy_id 买家ID
 * @param type $money_id 币ID
 * @param type $num 交易数量
 * @param type $price 交易单价
 * @param type $poundage 买家 手续费比例
 * @param type $zffs1
 * @param type $zffs2
 * @param type $zffs3
 * @return int
 */
function blockPdAdd($sell_id, $selluid, $buy_id, $buyuid, $bid, $num, $price, $poundage) {
    $data['sell_id'] = $sell_id; // 卖家id
    $data['sell_uid'] = $selluid; // 卖家会员id
    $data['buy_id'] = $buy_id; //买家id
    $data['buy_uid'] = $buyuid; //买家会员id
    $data['bid'] = $bid; // 钱包id
    $data['num'] = $num; // 交易数量
    $data['price'] = $price; // 交易价格
  	$data['is_type'] = 1;
    $data['money'] = $num * $price; // 数量 * 价格 =  总价
    $data['poundage'] = $poundage; // 手续费比例
    $data['poundage_num'] = $poundage * $num / 100; // 手续续折算数量
    $data['add_time'] = time();
    $res = M('block_trade')->add($data);
    if ($res) {
        return $res;
    } else {
        return 0;
    }
}

/**
 *   卖 统一计算 卖出后的分配方式
 * @param type $sell_uid   卖家
 * @param type $price   单价
 * @param type $num   数量
 * @param type $id   当条股票 交易规则
 * @param type $buy_uid   买家
 */
function unifiedOutPer($sell_uid, $price, $num, $id, $buy_uid = 0) {
    $config = M('block')->where(array('id' => $id))->find();
    $money = floatval($num * $price * zfCache('securityInfo.rate_per'));
    $note = $price . '/个，卖' . $num . '个' . $num . '*' . $price . '=' . $money . "\n";
    if ($config['sell_fee'] > 0) {
        $note .= '手续费' . $config['sell_fee'] . '%';
        $sellFee = $money * $config['sell_fee'] / 100;
        $yjMoney = $money - $sellFee;
    } else {
        $yjMoney = $money;
    }

    if ($config['s_p1'] > 0 && $config['s_m1'] > 0) {
        $moneyP1 = $yjMoney * $config['s_p1'] / 100;
        $note1 = $note . $yjMoney . '*' . $config['s_p1'] . '%= ' . $moneyP1;
        userMoneyLogAdd($sell_uid, $config['s_m1'], $moneyP1, 129, $note1, '', $buy_uid);
    }
    if ($config['s_p2'] > 0 && $config['s_m2'] > 0) {
        $moneyP2 = $yjMoney * $config['s_p2'] / 100;
        $note2 = $note . $yjMoney . '*' . $config['s_p2'] . '%= ' . $moneyP2;
        userMoneyLogAdd($sell_uid, $config['s_m2'], $moneyP2, 129, $note2, '', $buy_uid);
    }
    if ($config['s_p3'] > 0 && $config['s_m3'] > 0) {
        $moneyP3 = $yjMoney * $config['s_p3'] / 100;
        $note3 = $note . $yjMoney . '*' . $config['s_p3'] . '%= ' . $moneyP3;
        userMoneyLogAdd($sell_uid, $config['s_m3'], $moneyP3, 129, $note3, '', $buy_uid);
    }
    if ($config['s_p4'] > 0 && $config['s_m4'] > 0) {
        $moneyP4 = $yjMoney * $config['s_p4'] / 100;
        $note4 = $note . $yjMoney . '*' . $config['s_p4'] . '%= ' . $moneyP4;
        userMoneyLogAdd($sell_uid, $config['s_m4'], $moneyP4, 129, $note4, '', $buy_uid);
    }
    # 扣的手续费 计算加速业绩
    return TRUE;
}

/**
 * 每日自动涨价
 */
function dayAutoPrice() {
    $info = M('block')->where(array('id' => 1))->field('id,now_price,day_price')->find();
    if ($info['day_price'] > 0) {
        $data['zf_time'] = strtotime(date('Y-m-d'));
        if (!M('block_price')->where($data)->find()) {
            $data['bid'] = $info['id'];
            $data['zf_time'] = strtotime(date('Y-m-d'));
            $data['front_price'] = $info['now_price'];
            $data['after_price'] = $info['now_price'] + $info['day_price'];
            M('block')->where(array('id' => $info['id']))->save([
                'now_price' => $data['after_price']
            ]);
            return M('block_price')->add($data);
        }
    }
    return TRUE;
}

/**
 * 买方3分钟内没付款，订单就自动取消失效，卖方30分钟内没有确认收款自动扣信用
 */
function buySellRemitTimeBlock($userId = 0) {
    if ($userId > 0) {
        $data = array();
        $data['_string'] = 'sell_uid = ' . $userId . ' or buy_uid = ' . $userId;
        $data['is_type'] = array('elt', 2);
        //超时打款
        $tradeAll = M('block_trade')->where($data)->select();
    } else {
        $tradeAll = M('block_trade')->where(array('is_type' => array('elt', 2)))->select();
    }

    if (!empty($tradeAll)) {
        $config = M('block')->where(['id' => 1, 'statu' => 1])->find();
        foreach ($tradeAll as $v) {
            $user1 = getUserInfo($v['buy_uid']);

            switch ($v['is_type']) {
                case 1:
                    $dakuan_time = $v['add_time'] + $config['dakuan_time'] * 3600;
                    if ($dakuan_time < time()) {
                        M('block_trade')->where(array('id' => $v['id']))->save(array('is_type' => 3));
                        M('block_sell')->where(array('id' => $v['sell_id']))->save(array('status' => 3, 'return_num' => $v['num'], 'return_time' => time()));
                        M('block_buy')->where(array('id' => $v['buy_id']))->save(array('status' => 3, 'return_num' => $v['num'], 'return_time' => time()));
                        userBlockLogAdd($v['sell_uid'], $v['bid'], $v['num'], 113, $user1['account'] . '超时打款撤回');
                        if (userInfo($v['buy_uid'])['xinyu'] > 0) {
                            // 扣除未打款的哪个人的星誉信
                            M('users')->where(array('user_id' => $v['buy_uid']))->setDec('xinyu');
                            userAction($v['buy_uid'], '超时打款扣1信誉');
                        } else {
                            M('users')->where(array('user_id' => $v['buy_uid']))->save(array('frozen' => 2));
                            userLockLog($v['buy_uid'], '超时打款冻结');
                        }
                    }
                    break;
                case 2:
                    $shoukuan_time = $v['pay_time'] + $config['shoukuan_time'] * 3600;
                    if ($shoukuan_time < time()) {
                        M('block_trade')->where(array('id' => $v['id']))->save(array('is_type' => 4));
                        M('block_sell')->where(array('id' => $v['sell_id']))->save(array('status' => 3, 'return_num' => $v['num'], 'return_time' => time()));
                        M('block_buy')->where(array('id' => $v['buy_id']))->save(array('status' => 3, 'return_num' => $v['num'], 'return_time' => time()));
                        if (userInfo($v['sell_uid'])['xinyu'] > 0) {
                            //还要扣除不确认收款的那个用户的星誉信
                            M('users')->where(array('user_id' => $v['sell_uid']))->setDec('xinyu');
                            userAction($v['sell_uid'], '超时收款扣1信誉');
                        } else {
                            M('users')->where(array('user_id' => $v['sell_uid']))->save(array('frozen' => 2));
                            userLockLog($v['sell_uid'], '超时收款冻结');
                        }
                    }
                    break;
            }
        }
    }

    return TRUE;
}

/**
 * 确认收款倒计时
 * @param type $var
 * @return type
 */
function sellPaytimeBlock($var) {
    $mon = M('block_trade')->where(array('id' => $var))->field('pay_time,bid')->find();
    $aab2 = $mon['pay_time'] + (3600 * zfCache('securityInfo.shoukuan'));
    return date('Y-m-d H:i:s', $aab2);
}

/**
 * 确认付款倒计时
 * @param type $var
 * @return type
 */
function buyPayTimeBlock($var) {
    $mon = M('block_trade')->where(array('id' => $var))->field('add_time,bid')->find();
    $aab2 = $mon['add_time'] + 3600 * zfCache('securityInfo.dakuan');
    return date('Y-m-d H:i:s', $aab2);
}


/**
 * 新注册的会员 添加钱包
 * @param type $userId
 */
function getIntegral($uid) {
    $list = M('block_release_money')->where(['uid' => $uid, 'status' => 1])->select();
    if ($list) {
        foreach ($list as $v) {
          	if (zfCache('securityInfo.is_systec_test') == 1) {
                if ($v['last_time'] >= strtotime(date('Y-m-d'))) {
                    continue;
                }
            }
            userMoneyLogAdd($v['uid'], 1, $v['fh_money'], 158, '', '', $v['uid']);
            if (($v['fh_total'] + $v['fh_money']) >= $v['money']) {
                M('block_release_money')->save([
                    'id' => $v['id'],
                    'last_time' => time(),
                    'out_time' => time(),
                    'status' => 9,
                    'fh_num' => $v['fh_num'] + 1,
                    'fh_total' => $v['fh_total'] + $v['fh_money'],
                    'stay_money' => $v['stay_money'] - $v['fh_money']
                ]);
            } else {
                M('block_release_money')->save([
                    'id' => $v['id'],
                    'last_time' => time(),
                    'fh_num' => $v['fh_num'] + 1,
                    'fh_total' => $v['fh_total'] + $v['fh_money'],
                    'stay_money' => $v['stay_money'] - $v['fh_money']
                ]);
            }
        }
    }
    return TRUE;
}
