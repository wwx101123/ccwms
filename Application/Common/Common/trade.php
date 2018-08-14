<?php

function tradeBuySellIstype($id) {
    $data = array();
    $data[1] = "待交易";
    $data[2] = "交易中";
    $data[8] = "撤销交易";
    $data[9] = "交易完成";
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

function tradeBuySellIstype_en($id) {
    $data = array();
    $data[1] = "Deal with the transaction";
    $data[2] = "In the transaction";
    $data[8] = "Revocation of transactions";
    $data[9] = "Transaction completion";
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

function tradeIstype($id) {
    $data = array();
    $data[1] = "待付款";
    $data[2] = "己付款";
    $data[3] = "超时未付款";
    $data[4] = "超时未收款";
//    $data[5] = "投诉处理中";
    $data[6] = "投诉处理完成";
//    $data[7] = "卖家撤销";
//    $data[8] = "买家撤销";
    $data[9] = "交易完成";
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

function tradeIstype_en($id) {
    $data = array();
    $data[1] = "Pending payment";
    $data[2] = "Self payment";
    $data[3] = "Timeout payment";
    $data[4] = "Timeout unreceivables";
//    $data[5] = "投诉处理中";
    $data[6] = "Completion of complaint handling";
//    $data[7] = "卖家撤销";
//    $data[8] = "买家撤销";
    $data[9] = "Transaction completion";
    if ($id == '') {
        return $data;
    } else {
        return $data[$id];
    }
}

/**
 *  买入多单匹配交易
 * @param int $user_id 会员id
 * @param int $id  买入数据的当条id
 * @return bool
 */
function tradeAddPp($id = 0) {
    if ($id > 0) {
        $buyInfo = M('trade_buy')->where(array('id' => $id))->find();
    } else {
        $buyInfo = M('trade_buy')->where(array('is_type' => array('lt', 3)))->order('id asc')->find();
    }
    $sellInfo = M('trade_sell')->where(array('uid' => array('neq', $buyInfo['uid']), 'sell_price' => $buyInfo['buy_price'], 'mid' => $buyInfo['mid'], 'is_type' => array('lt', 3), 'stay_num' => array('gt', 0)))->order('sort desc')->limit('1')->find();
    if ($buyInfo['stay_num'] > 0 && $sellInfo['stay_num'] > 0) {
        $Pdnum = min($buyInfo['stay_num'], $sellInfo['stay_num']);
        if ($buyInfo['stay_num'] >= $Pdnum && $sellInfo['stay_num'] >= $Pdnum) {
            $zffs1 = ($buyInfo['zffs1'] == 1 && $sellInfo['zffs1'] == 1) ? 1 : 2;
            $zffs2 = ($buyInfo['zffs2'] == 1 && $sellInfo['zffs2'] == 1) ? 1 : 2;
            $zffs3 = ($buyInfo['zffs3'] == 1 && $sellInfo['zffs3'] == 1) ? 1 : 2;
            $res = tradePdAdd($sellInfo['id'], $sellInfo['uid'], $buyInfo['id'], $buyInfo['uid'], $buyInfo['mid'], $Pdnum, $buyInfo['buy_price'], $buyInfo['poundage'], $zffs1, $zffs2, $zffs3);
            $infoa = editBuyInfo($buyInfo, $Pdnum);
            $infob = editSellInfo($sellInfo, $Pdnum);
            tradeAddPp($id);
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
function tradeOutPp($id = 0) {
    if ($id > 0) {
        $sellInfo = M('trade_sell')->where(array('id' => $id))->find();
    } else {
        $sellInfo = M('trade_sell')->where(array('is_type' => array('lt', 3)))->order('id asc')->find();
    }
    if (!$sellInfo) {
        return true;
    }
    $buyInfo = M('trade_buy')->where(array('uid' => array('neq', $sellInfo['uid']), 'buy_price' => $sellInfo['sell_price'], 'mid' => $sellInfo['mid'], 'is_type' => array('lt', 3), 'stay_num' => array('gt', 0)))->order('sort desc')->limit('1')->find();
    if ($sellInfo['stay_num'] > 0 && $buyInfo['stay_num'] > 0) {
        $Pdnum = min($sellInfo['stay_num'], $buyInfo['stay_num']);
        if ($buyInfo['stay_num'] >= $Pdnum && $sellInfo['stay_num'] >= $Pdnum) {
            $model = new \Think\Model();
            $model->startTrans();
            $zffs1 = ($buyInfo['zffs1'] == 1 && $sellInfo['zffs1'] == 1) ? 1 : 2;
            $zffs2 = ($buyInfo['zffs2'] == 1 && $sellInfo['zffs2'] == 1) ? 1 : 2;
            $zffs3 = ($buyInfo['zffs3'] == 1 && $sellInfo['zffs3'] == 1) ? 1 : 2;
            $infoc = tradePdAdd($sellInfo['id'], $sellInfo['uid'], $buyInfo['id'], $buyInfo['uid'], $sellInfo['mid'], $Pdnum + $sellInfo['poundage_num'], $sellInfo['sell_price'], $buyInfo['poundage'], $zffs1, $zffs2, $zffs3);
            $infoa = editBuyInfo($buyInfo, $Pdnum);
            $infob = editSellInfo($sellInfo, $Pdnum);
            if ($infoa && $infob && $infoc) {
                $model->commit();
            } else {
                $model->rollback();
            }
            tradeOutPp($id);
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
function editBuyInfo($config, $pdNum) {
    $sellOutNum = $config['stay_num'] - $pdNum;
    if ($sellOutNum <= 0) {
        M('trade_buy')->where(array('id' => $config['id']))->save(array('is_type' => 9, 'out_time' => time(), 'stay_num' => $sellOutNum, 'zf_num' => $pdNum, 'zf_time' => time()));
        return 9;
    } else {
        if ($config['is_type'] == 1) {
            M('trade_buy')->where(array('id' => $config['id']))->save(array('status' => 2, 'stay_num' => $sellOutNum, 'zf_num' => $pdNum, 'zf_time' => time()));
        } else {
            M('trade_buy')->where(array('id' => $config['id']))->save(array('stay_num' => $sellOutNum, 'zf_num' => $pdNum, 'zf_time' => time()));
        }
        return 1;
    }
}

/**
 * 修改 股票卖家 数据
 * @param type $config 当条数据 数组
 * @param type $pdNum 本次交易数量
 */
function editSellInfo($config, $pdNum) {
    $sellOutNum = $config['stay_num'] - $pdNum;
    if ($sellOutNum <= 0) {
        M('trade_sell')->where(array('id' => $config['id']))->save(array('is_type' => 9, 'out_time' => time(), 'stay_num' => $sellOutNum, 'zf_num' => $pdNum, 'zf_time' => time()));
        return 9;
    } else {
        if ($config['is_type'] == 1) {
            M('trade_sell')->where(array('id' => $config['id']))->save(array('status' => 2, 'stay_num' => $sellOutNum, 'zf_num' => $pdNum, 'zf_time' => time()));
        } else {
            M('trade_sell')->where(array('id' => $config['id']))->save(array('stay_num' => $sellOutNum, 'zf_num' => $pdNum, 'zf_time' => time()));
        }
        return 1;
    }
}

/**
 * 添加交易数据表
 * @param type $sell_id 卖家ID
 * @param type $buy_id 买家ID
 * @param type $money_id 钱包ID
 * @param type $num 交易数量
 * @param type $price 交易单价
 * @param type $poundage 买家 手续费比例
 * @param type $zffs1 
 * @param type $zffs2
 * @param type $zffs3
 * @param int $dbrId 担保人id
 * @param float $dbrPoundage 担保人手续费
 * @return int
 */
function tradePdAdd($sell_id, $selluid, $buy_id, $buyuid, $mid, $num, $price, $poundage, $zffs1 = 1, $zffs2 = 1, $zffs3 = 1) {
    $data = [
        'sell_id' => $sell_id // 卖家id
        , 'sell_uid' => $selluid // 卖家会员id
        , 'buy_id' => $buy_id //买家id
        , 'buy_uid' => $buyuid //买家会员id
        , 'mid' => $mid // 钱包id
        , 'num' => $num - ($poundage * $num / 100)// 交易数量
        , 'price' => $price // 交易价格
        , 'money' => ($num - $poundage * $num / 100) * $price // 数量 * 价格 =  总价
        , 'poundage' => $poundage // 手续费比例
        , 'poundage_num' => $poundage * $num / 100 // 手续续折算数量
        , 'is_type' => 1 // 待付款
        , 'add_time' => time()
        , 'zffs1' => $zffs1
        , 'zffs2' => $zffs2
        , 'zffs3' => $zffs3
    ];

    $res = M('trade')->add($data);
    if ($res) {
        return $res;
    } else {
        return 0;
    }
}

/**
 * 确认收款倒计时
 * @param type $var
 * @return type
 */
function sellPaytime($var) {
    $mon = M('trade')->where(array('id' => $var))->field('pay_time,mid')->find();
    $config = M('trade_config')->where(array('mid' => $mon['mid']))->cache(true)->find();
    $aab2 = $mon['pay_time'] + (3600 * $config['sell_time']);
    return date('Y-m-d H:i:s', $aab2);
}

/**
 * 确认付款倒计时
 * @param type $var
 * @return type
 */
function buyPayTime($var) {
    $mon = M('trade')->where(array('id' => $var))->field('add_time,mid')->find();
    $config = M('trade_config')->where(array('mid' => $mon['mid']))->cache(true)->find();
    $aab2 = $mon['add_time'] + 3600 * $config['buy_time'];
    return date('Y-m-d H:i:s', $aab2);
}

/**
 * 确认交易 确认打款倒计时
 * @param type $var
 * @return type
 */
function tradePayTime($var) {
    $mon = M('trade')->where(array('id' => $var))->field('zf_time,mid')->find();
    $config = M('trade_config')->where(array('mid' => $mon['mid']))->cache(true)->find();
    $aab2 = $mon['zf_time'] + 3600 * $config['buy_time'];
    return date('Y-m-d H:i:s', $aab2);
}

/**
 * 发送信息
 * @param int $tuser_id 提供帮助会员
 * @param int $juser_id 接受帮助会员
 * @param int $type 发送类型 1是匹配发送 2 确认打款 3 确认收款
 */
function tradeMessage($id) {
    $tradeInfo = M('trade')->where(array('id' => $id))->field('sell_uid,buy_uid,mid,is_type')->find();
    switch ($tradeInfo['is_type']) {
        case 1:
            # 买入
            $sellMobile = D("UserView")->where(array('user_id' => $tradeInfo['sell_uid']))->field('mobile')->find();
            if (checkMobile($sellMobile['mobile'])) {
                sendSms($sellMobile['mobile'], '你的订单己出售,请留意');
            }
            break;
        case 2:
            # 打款
            $sellMobile = D("UserView")->where(array('user_id' => $tradeInfo['sell_uid']))->field('mobile')->find();
            if (checkMobile($sellMobile['mobile'])) {
                sendSms($sellMobile['mobile'], '你的订单己确认,请留意');
            }
            break;
        case 9:
            # 确认收款
            $buyMobile = D("UserView")->where(array('user_id' => $tradeInfo['buy_uid']))->field('mobile')->find();
            if (checkMobile($buyMobile['mobile'])) {
                sendSms($buyMobile['mobile'], '你的订单己确认,请留意');
            }
            break;
    }
}

/**
 * 买方3分钟内没付款，订单就自动取消失效，卖方30分钟内没有确认收款自动扣信用
 */

function buySellRemitTime($userId)
{
    $tradeConfig = M('trade_config')->where(array('id' => 1))->find();

    $data = array();
    $data['_string'] = 'sell_uid = ' . $userId . ' or buy_uid = ' . $userId;
    $data['is_type'] = 1;
    //超时打款
    $tradeAll = M('trade')->where($data)->select();
    if (!empty($tradeAll)) {
        foreach ($tradeAll as $v) {
            if ($v['add_time'] + $tradeConfig['buy_time'] * 3600 < time()) {
                $info['uid'] = $v['sell_uid'];
                $info['mid'] = $v['mid'];
                $info['sell_time'] = time();
                $info['sell_num'] = $v['num'];
                $info['sell_price'] = $v['price'];
                $info['money'] = $v['num'];
                $info['is_type'] = 1;
                $info['poundage'] = $tradeConfig['sell_fee'];
                $info['poundage_num'] = $v['poundage_num'];
                $info['stay_num'] = $v['num'];
                $info['total_num'] = $v['num'] + $v['poundage_num'];
                $info['tjr_uid'] = userInfo($v['sell_uid'])['tjr_id'];

                $res = M('trade_sell')->add($info);
                tradeOutPp($res);
//                        userMoneyLogAdd($v['sell_uid'], $v['mid'], $v['num'], 0, 110, '买家超时未付款退回');
                M('trade')->where(array('id' => $v['id']))->delete();
                if (userInfo($v['buy_uid'])['xinyu'] > 0) {
                    // 扣除未打款的哪个人的星誉信
                    M('users')->where(array('user_id' => $v['buy_uid']))->setDec('xinyu');
                    userAction($v['buy_uid'], '超时打款扣1信誉');
                } else {
                    M('users')->where(array('user_id' => $v['buy_uid']))->save(array('frozen' => 2));
                    userLockLog($v['buy_uid'], '超时打款冻结');
                }
            }
        }
    }

    $info = array();
    $info['_string'] = 'sell_uid = ' . $userId . ' or buy_uid = ' . $userId;
    $info['is_type'] = 2;

    //超时收款
    $tradeAllsell = M('trade')->where($info)->select();
    if (!empty($tradeAllsell)) {
        foreach ($tradeAllsell as $v) {
            if ($v['pay_time'] + $tradeConfig['sell_time'] * 3600 < time()) {
                M('trade')->where(array('id' => $v['id']))->save(array('is_type' => 4));

                if ($v['poundage_num'] > 0) {
                    userMoneyLogAdd($v['buy_uid'], $v['mid'], $v['num'], 110, '卖家：' . getUserInfo($v['sell_uid'])['account']);
                } else {
                    userMoneyLogAdd($v['buy_uid'], $v['mid'], $v['num'] + $v['poundage_num'], 110, '卖家：' . getUserInfo($v['sell_uid'])['account']);
                }
                if (userInfo($v['sell_uid'])['xinyu'] > 0) {
                    //还要扣除不确认收款的那个用户的星誉信
                    M('users')->where(array('user_id' => $v['sell_uid']))->setDec('xinyu');
                    userAction($v['sell_uid'], '超时收款扣1信誉');
                } else {
                    M('users')->where(array('user_id' => $v['sell_uid']))->save(array('frozen' => 2));
                    userLockLog($v['sell_uid'], '超时收款冻结');
                }
            }
        }
    }
}
