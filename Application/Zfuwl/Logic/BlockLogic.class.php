<?php

namespace Zfuwl\Logic;

use Think\Model\RelationModel;

class BlockLogic extends RelationModel {

    public function addBlockConfig($post) {
        $info = M('block')->where(array('id' => $post['id']))->find();
        $per = $post['s_p1'] + $post['s_p2'] + $post['s_p3'] + $post['s_p4'];
        if ($per > 100) {
            return array('status' => -1, 'msg' => '当前分配比例' . $per . ',不能大于100');
        }
        $data['name_cn'] = $post['name_cn'] ? $post['name_cn'] : FALSE;
        $data['name_en'] = $post['name_en'] ? $post['name_en'] : FALSE;
        $data['thigh'] = $post['thigh'] ? $post['thigh'] : FALSE;
        $data['taotal'] = $post['taotal'] ? $post['taotal'] : FALSE;
        $data['now_price'] = $post['now_price'] ? $post['now_price'] : FALSE;
        $data['logo'] = $post['logo'] ? $post['logo'] : FALSE;
        $data['statu'] = $post['statu'] ? $post['statu'] : 1;
        $data['float_price'] = $post['float_price'] ? $post['float_price'] : 1;
        $data['buy_mid'] = $post['buy_mid'] ? $post['buy_mid'] : 1;
        $data['buy_low'] = $post['buy_low'] ? $post['buy_low'] : 1;
        $data['buy_bei'] = $post['buy_bei'] ? $post['buy_bei'] : 1;
        $data['buy_fee'] = $post['buy_fee'] > 0 ? $post['buy_fee'] : 0;
        $data['sell_low'] = $post['sell_low'] ? $post['sell_low'] : 1;
        $data['sell_bei'] = $post['sell_bei'] ? $post['sell_bei'] : 1;
        $data['sell_fee'] = $post['sell_fee'] > 0 ? $post['sell_fee'] : 0;
      	$data['day_sell_per'] = $post['day_sell_per'] > 0 ? $post['day_sell_per'] : 0;
        $data['dbr_fee'] = $post['dbr_fee'] ? $post['dbr_fee'] : 1;
        $data['s_p1'] = $post['s_p1'] > 0 ? $post['s_p1'] : 0;
        $data['s_m1'] = $post['s_m1'] > 0 ? $post['s_m1'] : 0;
        $data['s_p2'] = $post['s_p2'] > 0 ? $post['s_p2'] : 0;
        $data['s_m2'] = $post['s_m2'] > 0 ? $post['s_m2'] : 0;
        $data['s_p3'] = $post['s_p3'] > 0 ? $post['s_p3'] : 0;
        $data['s_m3'] = $post['s_m3'] > 0 ? $post['s_m3'] : 0;
        $data['s_p4'] = $post['s_p4'] > 0 ? $post['s_p4'] : 0;
        $data['s_m4'] = $post['s_m4'] > 0 ? $post['s_m4'] : 0;
        $data['day_price'] = $post['day_price'] ? $post['day_price'] : 0.00;
        $data['dakuan_time'] = $post['dakuan_time'] > 0 ? $post['dakuan_time'] : 0;
        $data['shoukuan_time'] = $post['shoukuan_time'] > 0 ? $post['shoukuan_time'] : 0;
        if ($post['id'] > 0) {
            if ($post['now_price'] != $info['now_price']) {
                $data['bid'] = $post['id'];
                $data['zf_time'] = time();
                $data['front_price'] = $info['now_price'];
                $data['after_price'] = $post['now_price'];
                $res = M('block_price')->add($data);
            }
            $resId = M('block')->where(array('id' => $post['id']))->save($data);
            if (!$resId) {
                return array('status' => -1, 'msg' => '操作失败');
            } else {
                return array('status' => 1, 'msg' => '操作成功');
            }
        }
    }

    public function editConfigInfo($post) {	
        if ($post['money_id'] <= 0) {
            return array('status' => -1, 'msg' => '请选择转出钱包');
        }
        if ($post['bid'] <= 0) {
            return array('status' => -1, 'msg' => '请选择转转账钱包');
        }
        /*if ($post['money_id'] == $post['bid']) {
            return array('status' => -1, 'msg' => '转出钱包不能与转入钱包相同');
        } else*/
        if ($post['bid'] && $post['money_id']) {
            $num = D('block_transform')->where(array('mid' => $post['money_id'], 'bid_a' => $post['bid']))->count();
            if ($post['id'] > 0) {
                if ($num > 1) {
                    return array('status' => -1, 'msg' => '己存在相同规则');
                }
            } else {
                if ($num > 0) {
                    return array('status' => -1, 'msg' => '己存在相同规则');
                }
            }
            $post['money_id'] && $data['mid'] = $post['money_id'];
            $post['bid'] && $data['bid_a'] = $post['bid'];
            $post['low'] && $data['low'] = $post['low'];
            $post['bei'] && $data['bei'] = $post['bei'];
            $data['out'] = $post['out'] > 0 ? $post['out'] : 0;
            $data['fee'] = $post['fee'] > 0 ? $post['fee'] : 0;
            $data['level_id'] = $post['level_id'] > 0 ? $post['level_id'] : 0;
            $data['day_num'] = $post['day_num'] > 0 ? $post['day_num'] : 0;
            $data['day_total'] = $post['day_total'] > 0 ? $post['day_total'] : 0;
//            $data['mid'] = $post['mid'] > 0 ? $post['mid'] : 0;
            $data['mpr'] = $post['mpr'] > 0 ? $post['mpr'] : 0;
            $data['dpr'] = $post['dpr'] > 0 ? $post['dpr'] : 0;
            $post['statu'] && $data['statu'] = $post['statu'];
            $post['trial'] && $data['trial'] = $post['trial'];
            if ($post['id'] > 0) {
                $resId = M('block_transform')->where(array('id' => $post['id']))->save($data);
            } else {
                $resId = M('block_transform')->add($data);
            }
            if (!$resId) {
                return array('status' => -1, 'msg' => '修改失败');
            } else {
                return array('status' => 1, 'msg' => '修改成功');
            }
        }
    }

    public function editConfigInfo2($post) {
//        if ($post['money_id'] <= 0) {
//            return array('status' => -1, 'msg' => '请选择转出钱包');
//        }
        if ($post['bid'] <= 0) {
            return array('status' => -1, 'msg' => '请选择转入生命链');
        }
//        if ($post['money_id'] == $post['bid']) {
//            return array('status' => -1, 'msg' => '转出钱包不能与转入钱包相同');
//        } else {
        $num = D('block_change')->where(array('bid' => $post['bid']))->count();
        if ($post['id'] > 0) {
            if ($num > 1) {
                return array('status' => -1, 'msg' => '己存在相同规则');
            }
        } else {
            if ($num > 0) {
                return array('status' => -1, 'msg' => '己存在相同规则');
            }
        }
        $post['money_id'] && $data['money_id'] = $post['money_id'];
        $post['bid'] && $data['bid'] = $post['bid'];
        $post['low'] && $data['low'] = $post['low'];
        $post['bei'] && $data['bei'] = $post['bei'];
        $data['out'] = $post['out'] > 0 ? $post['out'] : 0;
        $data['fee'] = $post['fee'] > 0 ? $post['fee'] : 0;
        $data['level_id'] = $post['level_id'] > 0 ? $post['level_id'] : 0;
        $data['day_num'] = $post['day_num'] > 0 ? $post['day_num'] : 0;
        $data['day_total'] = $post['day_total'] > 0 ? $post['day_total'] : 0;
        $data['mid'] = $post['mid'] > 0 ? $post['mid'] : 0;
        $data['mpr'] = $post['mpr'] > 0 ? $post['mpr'] : 0;
        $data['dpr'] = $post['dpr'] > 0 ? $post['dpr'] : 0;
        $post['statu'] && $data['statu'] = $post['statu'];
        $post['trial'] && $data['trial'] = $post['trial'];
        if ($post['id'] > 0) {
            $resId = M('block_change')->where(array('id' => $post['id']))->save($data);
        } else {
            $resId = M('block_change')->add($data);
        }
        if (!$resId) {
            return array('status' => -1, 'msg' => '修改失败');
        } else {
            return array('status' => 1, 'msg' => '修改成功');
        }
//        }
    }

    public function userTransAdd($post, $user_id) {

        $user = getUserInfo($user_id);
        if (webEncrypt($post['secpwd']) != $user['secpwd']) {
            return array('status' => -1, 'msg' => '交易密码 ' . $post['secpwd'] . '验证失败!');
        }
        $post['mid'] = intval($post['mid']);
        $post['bid'] = intval($post['bid']);
        $post['money'] = floatval($post['money']);

        if ($post['mid'] <= 0) {
            return array('status' => -1., 'msg' => '请输入卖出钱包');
        }

        if ($post['bid'] <= 0) {
            return array('status' => -1, 'msg' => '请输入买入钱包');
        }
        if ($post['money'] <= 0) {
            return array('status' => -1, 'msg' => '请输入兑换个数');
        }

        if ($post['fenNum'] <= 0) {
            return array('status' => -1, 'msg' => '请输入正确的兑换个数');
        }
//        if ($user['level'] > 0) {
//            $info = M('block_transform')->where(array('mid' => $post['mid'], 'statu' => 1))->find();
//            if (!$info) {
        $info = M('block_transform')->where(array('mid' => $post['mid'], 'statu' => 1))->find();
//            }
//        }

        if (!$info) {
            return array('status' => -1, 'msg' => '操作失败!');
        }

        if ($info['day_num'] > 0) {
            $dayNum = M('block_transform_log')->where(['uid' => $user_id, 'mid' => $post['mid'], 'zf_time' => ['egt', strtotime(date('Ymd'))]])->count();
            if ($dayNum + 1 > $info['day_num']) {
                return array('status' => -1, 'msg' => '今日最高还可兑换' . ($info['day_num'] - ($dayNum + 1)) . '次');
            }
        }
        if ($info['day_total'] > 0) {
            $dayMoney = floatval(M('block_transform_log')->where(array('uid' => $user_id, 'bid' => $post['mid'], 'statu' => 1))->sum('money'));
            if (($dayMoney + $post['money']) > $info['day_total']) {
                return array('status' => -1, 'msg' => '今日最高还可兑换' . ($info['day_total'] - $dayMoney));
            }
        }

        if ($info['mid'] > 0 && $info['mpr'] > 0) {
            $dMoney = $post['money'] * $info['mpr'] / 100;
        } else {
            $dMoney = 0;
        }

        if ($post['money'] < $info['low'] || $post['money'] % $info['bei'] != 0) {
            return array('status' => -1, 'msg' => '金额输入错误，兑换' . $info['low'] . '起且是' . $info['bei'] . '倍数!');
        }
        if ($info['fee'] > 0) {
            $poundage = $post['money'] * $info['fee'] / 100;
            $enterMoney = $post['money'] - $poundage - $dMoney;
            $toMoney = $post['money'];
            $enterNote = $post['note'] . ',' . $post['money'] . '手续费' . $poundage;
        } else {
            $toMoney = $enterMoney = $post['money'] - $dMoney;
            $enterNote = $post['note'];
            $poundage = 0;
        }

        if ($post['fenNum'] > usersMoney($user_id, $post['mid'], 1)) {
            return array('status' => -1, 'msg' => moneyList($post['mid'], 1) . '余额不足!');
        }

        $data = [
            'uid' => $user_id
            , 'bid' => $post['bid']
            , 'mid' => $post['mid']
            , 'money' => $toMoney
            , 'zf_time' => time()
            , 'poundage' => $info['poundage'] ? $info['poundage'] : 0
            , 'type_money' => $enterMoney
            , 'note' => $post['note']
            , 'num' => $enterMoney / blockList(1, 2)
            , 'dmoney' => $dMoney
            , 'dmid' => $info['mid']
            , 'fee' => $info['fee']
            , 'fee_money' => $poundage
            , 'statu' => $info['trial']
            , 'per' => blockList(1, 2)
            , 'jifen' => $toMoney * blockList(1, 2)
        ];

        // $data['uid'] = $user_id;
        // $data['mid'] = $post['mid'];
        // $data['bid'] = 1;
        // $data['money'] = $toMoney;
        // $data['zf_time'] = time();
        // $data['poundage'] = $info['poundage'] ? $info['poundage'] : 0;
        // $data['type_money'] = $enterMoney;
        // $data['note'] = $post['note'];
        // $data['num'] = $enterMoney / blockList(1, 2);
        // $data['dmoney'] = $dMoney;
        // $data['dmid'] = $info['mid'];
        // $data['fee'] = $info['fee'];
        // $data['fee_money'] = $poundage;
        $model = new \Think\Model();
        $model->startTrans();
        $A = M('block_transform_log')->add($data);
        $B = userMoneyLogAdd($user_id, $post['mid'], '-' . $post['fenNum'], 108, '兑换' . blockList($post['bid'], 1) . $enterNote);
        $D = userAction($user_id, moneyList($post['mid']) . '兑换' . blockList($post['bid'], 1));
        $F = userBlockLogAdd($user_id, $post['bid'], $toMoney, 108, moneyList($post['mid']) . '兑换');

        if ($A && $B && $D && $F) {
            $model->commit();
            return array('status' => 1, 'msg' => '操作成功!');
        } else {
            $model->rollback();
            return array('status' => -1, 'msg' => '操作失败!');
        }
    }

    public function userChangeAdd($post, $userId)
    {

        $user = getUserInfo($userId);
        if (webEncrypt($post['secpwd']) != $user['secpwd']) {
            return array('status' => -1, 'msg' => '交易密码 ' . $post['secpwd'] . '验证失败!');
        }
        if (intVal($post['bid']) <= 0) {
            return ['status' => -1, 'msg' => '请选择转账钱包'];
        }

        if (!$post['address']) {
            return ['status' => -1, 'msg' => '请输入钱包地址'];
        }

        $address = M('block_user')->where(['address' => $post['address']])->find();
        if (!$address) {
            return ['status' => -1, 'msg' => '请输入正确的钱包地址'];
        }

        $toAccount = M('users')->where(['user_id' => $address['uid']])->find();
        if ($userId == $toAccount['user_id']) {
            return ['status' => -1, 'msg' => '不能自己转自己'];
        }

        if (!$toAccount) {
            return ['status' => -1, 'msg' => '对方账号不存在'];
        }


        if (empty($post['money'])) {
            return ['status' => -1, 'msg' => '请输入转出个数'];
        }

        $info = M('block_change')->where(array('bid' => $post['bid'], 'statu' => 1))->find();
        if (!$info) {
            return ['status' => -1, 'msg' => '操作失败'];
        }

        $low = $dayOut = $bei = $fee = 0;
        $low = floatval($info['low']);
        $bei = floatval($info['bei']);
        $fee = floatval($info['fee']);
        $changeType = 1;


        if ($post['money'] < $low || $post['money'] % $bei != 0) {
            return ['status' => -1, 'msg' => '金额输入错误，转账' . $low . '起且是' . $bei . '倍数!'];
        }
        if ($dayOut > 0) {
            $daydChangeTotal = floatval(M('block_change_log')->where(['uid' => $user['user_id'], 'change_type' => $changeType])->sum('money'));
            if ($daydChangeTotal + $post['money'] > $dayOut) {
                $allowChangeMoney = $dayOut - $daydChangeTotal;
                $allowChangeMoney = ($allowChangeMoney > 0 ? $allowChangeMoney : 0);
                return ['status' => -1, 'msg' => '今天最多还能转' . $allowChangeMoney];
            }
        }

        if ($fee > 0) {
            $poundage = $post['money'] * $fee / 100;
//            if ($info['fee_type'] == 1) {// 扣转出方手续费
            $toMoney = $post['money'] + $poundage;
            $enterMoney = $post['money'];
            $toNote = ',' . $post['money'] . '，转出手续费' . $poundage . '%';
//            } else {// 扣转入方手续费
//                $toMoney = $post['money'];
//                $enterMoney = $post['money'] - $poundage;
//                $enterNote = $post['money'] . '，转入手续费' . $poundage . '%';
//            }
        } else {
            $toMoney = $enterMoney = $post['money'];
        }

        if ($toMoney > userBlock($userId, $post['bid'], 1)) {
            return array('status' => -1, 'msg' => blockList($post['bid'], 1) . '余额不足');
        }

        $data = [
            'uid' => $userId
            , 'to_uid' => $toAccount['user_id']
            , 'bid' => $post['bid']
            , 'type_id' => $post['bid']
            , 'money' => $toMoney
            , 'zf_time' => time()
            , 'fee_money' => $fee
            , 'poundage' => $poundage
            , 'to_money' => $enterMoney
            , 'note' => $post['note'] . $toNote
            , 'change_type' => $changeType
            , 'per' => blockList(1, 2)
            , 'jifen' => $toMoney * blockList(1, 2)
        ];

        $model = new \Think\Model();
        $model->startTrans();
        $A = M('block_change_log')->add($data);
        $B = userBlockLogAdd($userId, $post['bid'], '-' . $toMoney, 105, '转至' . userInfo($toAccount['user_id'])['account']);
        $C = userBlockLogAdd($toAccount['user_id'], $info['bid'], $enterMoney, 105, $user['account'] . '转入', '', $userId);
        $D = userAction($userId, blockList($post['bid']) . '转' . $toMoney . '至' . userInfo($toAccount['user_id'])['account'] . $toNote);
        if ($A && $B && $C && $D) {
            $model->commit();
            return array('status' => 1, 'msg' => '操作成功!');
        } else {
            $model->rollback();
            return array('status' => -1, 'msg' => '操作失败!');
        }
    }

    public function buyAdd($post, $user_id) {
        $userInfo = getUserInfo($user_id);
        if ($post['price'] == '') {
            return array('status' => -1, 'msg' => '请输入买入价格');
        }
        if ($post['buynum'] == '') {
            return array('status' => -1, 'msg' => '请输入买入数量');
        }
        if ($post['buySecpwd'] == '') {
            return array('status' => -1, 'msg' => '请输入交易密码');
        }

        $sellData = M('block_sell')->where(['status' => 1])->order('add_time desc')->find();
        if (empty($sellData)) {
            return ['status' => -1, 'msg' => '暂时没有卖出的，请稍后再试'];
        }

        $sell = M('block_sell')->where(['status' => 1, 'num' => $post['buynum'], 'price' => $post['price']])->order('add_time desc')->find();
        if (empty($sell)) {
            return ['status' => -1, 'msg' => '卖出数量不足，你现在只可以购买' . $sellData['num'] . '个，并且价格是' . $sellData['price']];
        }

        if ($userInfo['secpwd'] != webEncrypt($post['buySecpwd'])) {
            return array('status' => -1, 'msg' => '交易密码错误');
        }
        $config = M('block')->where(array('id' => 1))->field('buy_mid,buy_low,buy_bei,buy_fee,dbr_fee')->find();

        if ($post['buynum'] < $config['buy_low'] || $post['buynum'] % $config['buy_bei'] != 0) {
            return array('status' => -1, 'msg' => '买入数量必须大于最低买入数量' . $config['buy_low'] . '且是' . $config['buy_bei'] . '的倍数');
        }

        // 查出用户矿金的余额
        $userMoney = usersMoney($user_id, 2, 3);
        $buyMoney = $post['buynum'] * $post['price'] * zfCache('securityInfo.rate_per');
        if ($userMoney < $buyMoney) {
            return ['status' => -1, 'msg' => '矿金余额不足'];
        }

        $addArr = array(
            'bid' => 1,
            'uid' => $userInfo['user_id'],
            'add_time' => time(),
            'num' => $post['buynum'],
            'price' => $post['price'],
            'total' => $post['buynum'] * $post['price'],
            'poundage' => $config['buy_fee'],
            'status' => 1,
            'stay_num' => $post['buynum']
        );
        /* # 担保人
          $post['dbr_user_id'] = intval($post['dbr_user_id']);
          if ($post['dbr_user_id'] > 0) {
          $addArr['dbr_uid'] = $post['dbr_user_id'];
          $addArr['dbr_poundage'] = $config['dbr_fee'];
          }
          # 担保人 */

        $add = M('block_buy')->add($addArr);
        if ($add) {
            blockAddPp($add);
            userMoneyLogAdd($user_id, 2, '-' . $buyMoney, 157, '购买货币');
            return array('status' => 1, 'msg' => '买入申请成功');
        } else {
            return array('stauts' => -1, 'msg' => '买入申请失败');
        }
    }

    public function buyOneAdd($post, $user_id) {
        if ($post['id'] <= 0) {
            return array('status' => -1, 'msg' => '网络错误，刷新页面后重试');
        }
        if ($post['num'] <= 0) {
            return array('status' => -1, 'msg' => '请输入买入数量');
        }
        $sellInfo = M('block_sell')->where(array('id' => $post['id']))->find();
        if ($sellInfo['stay_num'] < $post['num']) {
            return array('status' => -1, 'msg' => '超出可买数量' . $sellInfo['stay_num']);
        }

        $userInfo = getUserInfo($user_id);
        if ($post['buySecpwd'] == '') {
            return array('status' => -1, 'msg' => '请输入交易密码');
        }
        if ($userInfo['secpwd'] != webEncrypt($post['buySecpwd'])) {
            return array('status' => -1, 'msg' => '交易密码错误');
        }

        $config = M('block')->where(array('id' => 1))->field('id,buy_mid,buy_low,buy_bei,buy_fee,dbr_fee')->find();
        if ($post['num'] < $config['buy_low'] || $post['num'] % $config['buy_bei'] != 0) {
            return array('status' => -1, 'msg' => '买入数量必须大于最低买入数量' . $config['buy_low'] . '且是' . $config['buy_bei'] . '的倍数');
        }
        $model = new \Think\Model();
        $model->startTrans();
        $addArr = array(
            'bid' => $config['id'],
            'uid' => $userInfo['user_id'],
            'buy_time' => time(),
          	'zf_time' => time(),
            'add_time' => time(),
            'out_time' => time(),
            'num' => $post['num'],
            'price' => $sellInfo['price'],
            'total' => $post['num'] * $sellInfo['price'],
            'poundage' => $config['buy_fee'],
            'status' => 1,
            'stay_num' => 0,
            'zf_num' => $post['num'],
        );
        # 担保人 s
        $post['dbr_user_id'] = intval($post['dbr_user_id']);
        if ($post['dbr_user_id'] > 0) {
            $addArr['dbr_uid'] = $post['dbr_user_id'];
            $addArr['dbr_poundage'] = $config['dbr_fee'];
        }
        # 担保人 e
        $A = M('block_buy')->add($addArr);
//        $B = editSellInfo($sellInfo, $post['num']);
        $C = blockPdAdd($sellInfo['id'], $sellInfo['uid'], $A, $user_id, $config['id'], $post['num'], $sellInfo['price'], $config['buy_fee']);
        if ($A && $C) {
            M('block_buy')->where(['id' => $A])->save(['status' => 2]);
            M('block_sell')->where(['id' => $post['id']])->save(['status' => 2, 'stay_num' => '']);
            $model->commit();
            return array('status' => 1, 'msg' => '操作成功!');
        } else {
            $model->rollback();
            return array('status' => -1, 'msg' => '操作失败!');
        }
    }

    public function sellAdd($post, $user_id)
    {
        $userInfo = getUserInfo($user_id);
      	$user_bank = D("UserView")->where(['user_id' => $user_id])->field('bank_address,opening_id,bank_account,bank_name')->find();
        if ($user_bank['bank_name'] == '' || $user_bank['bank_account'] == '' || $user_bank['opening_id'] <= 0) {
            return array('status' => -1, 'msg' => '请在安全管理把银行卡资料填写');
        }

        if (empty($post['sellnum'])) {
            return array('status' => -1, 'msg' => '请输入卖出数量');
        }
        if (empty($post['sellSecpwd'])) {
            return array('status' => -1, 'msg' => '请输入交易密码');
        }
        if ($userInfo['secpwd'] != webEncrypt(intval($post['sellSecpwd']))) {
            return array('status' => -1, 'msg' => '交易密码错误');
        }
        $config = M('block')->where(array('id' => 1))->field('sell_low,sell_bei,sell_fee,day_sell_per')->find();

        if ($post['sellnum'] < $config['sell_low'] || $post['sellnum'] % $config['sell_bei'] != 0) {
            return array('status' => -1, 'msg' => '卖出数量必须大于最低卖出数量' . $config['sell_low'] . '且是' . $config['sell_bei'] . '的倍数');
        }
        $userBlock = M('block_user')->where(['uid' => $userInfo['user_id']])->find();

        if ($post['sellnum'] > usersBlock($userInfo['user_id'], 1, 3)) {
            return array('status' => -1, 'msg' => blockList($post['bid']) . '不足');
        }

        if ($config['sell_fee'] > 0) {
            $poundageNum = (int)($post['sellnum'] * $config['sell_fee'] / 100);
        } else {
            $poundageNum = 0;
        }
        if ($config['day_sell_per'] > 0) {
            $daySellNum = M('block_sell')->where(['uid' => $userInfo['user_id'], 'add_time' => ['egt', strtotime(date('Y-m-d'))]])->sum('num');
            $userTotalNum = $userBlock['money'] + $daySellNum;
            if (($daySellNum + $post['sellnum']) > ($userTotalNum * $config['day_sell_per'] / 100)) {
                return array('status' => -1, 'msg' => '每日最多可售' . $config['day_sell_per'] . '%');
            }
        }

        $sellArr = [
            'bid' => 1,
            'uid' => $userInfo['user_id'],
            'add_time' => time(),
            'num' => $post['sellnum'],
            'price' => $post['price'],
            'total' => $post['sellnum'] * $post['price'],
            'poundage' => $config['sell_fee'],
            'status' => 1,
            'stay_num' => $post['sellnum'] - $poundageNum
        ];
        $add = M('block_sell')->add($sellArr);
        if ($add) {
            userBlockLogAdd($userInfo['user_id'], $post['bid'], '-' . $post['sellnum'], 2, '申请卖出');
            blockOutPp($add);
            return array('status' => 1, 'msg' => '卖出申请成功');
        } else {
            return array('stauts' => -1, 'msg' => '卖出申请失败');
        }
    }

    public function sellOneAdd($post, $user_id) {
        if ($post['id'] <= 0) {
            return array('status' => -1, 'msg' => '网络错误，刷新页面后重试');
        }
        if ($post['num'] <= 0 || $post['num'] == '') {
            return array('status' => -1, 'msg' => '请输入数量');
        }
        $buyInfo = M('trade_buy')->where(array('id' => $post['id']))->find();
        if ($buyInfo['stay_num'] < $post['num']) {
            return array('status' => -1, 'msg' => '超出买入数量' . $buyInfo['stay_num']);
        }

        $userInfo = getUserInfo($user_id);
        if ($post['sellSecpwd'] == '') {
            return array('status' => -1, 'msg' => '请输入二级密码');
        }
        if ($userInfo['secpwd'] != webEncrypt($post['sellSecpwd'])) {
            return array('status' => -1, 'msg' => '二级密码错误');
        }
        if ($post['num'] > usersBlock($userInfo['user_id'], 1, 3)) {
            return array('status' => -1, 'msg' => blockList(1) . '不足');
        }
        $config = M('block')->where(array('id' => 1))->field('id,sell_low,sell_bei,sell_fee,dbr_fee')->find();
        if ($post['num'] < $config['sell_low'] || $post['num'] % $config['sell_bei'] != 0) {
            return array('status' => -1, 'msg' => '必须大于最低买入数量' . $config['sell_low'] . '且是' . $config['sell_bei'] . '的倍数');
        }
        $model = new \Think\Model();
        $model->startTrans();
        $addArr = array(
            'mid' => $config['id'],
            'uid' => $userInfo['user_id'],
            'sell_time' => time(),
            'zf_time' => time(),
            'out_time' => time(),
            'sell_num' => $post['num'],
            'sell_price' => $buyInfo['buy_price'],
            'money' => $post['num'] * $buyInfo['buy_price'],
            'poundage' => $config['sell_fee'],
            'is_type' => 9,
            'stay_num' => 0,
            'zf_num' => $post['num'],
        );

        $A = M('trade_sell')->add($addArr);
        $B = editBuyInfo($buyInfo, $post['num']);
        $C = tradePdAdd($A, $user_id, $buyInfo['id'], $buyInfo['uid'], $config['id'], $post['num'], $buyInfo['buy_price'], $buyInfo['poundage'], 1, 1, 1, intval($buyInfo['dbr_uid']), $config['dbr_fee']);
        $D = userBlockLogAdd($userInfo['user_id'], 1, '-' . $post['num'], 2, '申请卖出');
        if ($A && $B && $C && $D) {
            $model->commit();
            return array('status' => 1, 'msg' => '操作成功!');
        } else {
            $model->rollback();
            return array('status' => -1, 'msg' => '操作失败!');
        }
    }

  	
  	/**
     * 会员上传打款凭证
     * @param array $post 提交数据
     * @return array 返回数据
     */
    public function buyPayAdd($post) {
        if ($post['id'] <= 0) {
            return array('status' => -1, 'msg' => '操作失败');
        }
        $tu = M('block_trade')->where(array('id' => $post['id']))->find();
        if ($tu['pay_time'] > 0) {
            return array('status' => -1, 'msg' => '改订单已付款！');
        }

        if ($tu['sell_uid'] == $post['uid']) {
            return array('status' => -1, 'msg' => '你是卖出方！');
        }

        $res = M('block_trade')->where(array('id' => $post['id']))->save(array('pay_time' => time(), 'is_type' => 2));
        if ($res) {
            return array('status' => 1, 'msg' => '付款成功');
        } else {
            return array('status' => -1, 'msg' => '付款失败');
        }
    }

    /**
     * 确认收款
     * @param array $post 提交的数据
     * @return array 操作结果
     */
    public function confirmPay($post)
    {
        if (!$post['id']) {
            return array('status' => -1, 'msg' => '操作失败，请刷新页面后重试');
        }
        $list = M('block_trade')->where(array('id' => $post['id']))->find();


        $sellList = M('block_sell')->where(['id' => $list['sell_id']])->find();

        if ($list['is_type'] == 1) {
            return array('status' => -1, 'msg' => '请等待对方付款完成!');
        }


        if ($list['is_type'] == 2 || $list['is_type'] == 4) {
            if (M('block_trade')->where(array('id' => $list['id']))->save(array('is_type' => 9, 'zf_time' => time()))) {
                $tradebuy = M('block_buy')->where(array('buy_id' => $list['buy_id']))->find();

                if ($tradebuy['stay_num'] <= 0) {
                    M('block_buy')->where(array('id' => $list['buy_id']))->save(array('status' => 9, 'out_time' => time()));
                }

                $tradesell = M('block_sell')->where(array('sell_id' => $list['sell_id']))->find();

                if ($tradesell['stay_num'] <= 0) {
                    M('block_sell')->where(array('id' => $list['sell_id']))->save(array('status' => 9, 'out_time' => time()));
                }

                $user = getUserInfo($list['sell_uid']);

                userBlockLogAdd($list['buy_uid'], $list['bid'], $list['num'], 111, '卖家：' . $user['account']);

                // 计算总资产
                $user['invest_money'] <= $list['money'] ? $list['money'] = $user['invest_money'] : $list['money'] = $list['money'];
                //M('users')->where(['user_id' => $list['sell_uid']])->setDec('invest_money', $list['money']);
				//userAction($list['sell_uid'], '总投资金额 - ' . $list['money']);
              		
                return array('status' => 1, 'msg' => '操作成功');
            } else {
                return array('status' => -1, 'msg' => '操作失败');
            }
        } else {
            return array('status' => -1, 'msg' => '请勿重复确认');
        }
    }
  
  
  	/**
     * 管理员撤回交易
     */
    public function tradeOut($post)
    {
        if (!$post['id']) {
            return ['status' => -1, 'msg' => '网络错误，请刷新后重试'];
        }

        $list = M('block_trade')->where(['id' => $post['id']])->find();


        $info = M('block_buy')->where(['id' => $list['buy_id']])->delete();
        $res = M('block_sell')->where(['id' => $list['sell_id']])->save(['stay_num' => $list['num'], 'status' => 1]);
        $save = M('block_trade')->where(['id' => $post['id']])->save(['is_type' => 6]);
        if ($info && $res && $save) {
            return ['status' => 1, 'msg' => '取消成功'];
        } else {
            return ['status' => -1, 'msg' => '取消失败'];
        }
    }
  		
  	public function returnben($data, $userId)
    {
        $money = intval($data['money']);
        $deduct_fen = intval($data['deduct_fen']);
        if ($money <= 0) {
            return ['status' => -1, 'msg' => '请输入回本金额'];
        }

        if ($deduct_fen <= 0) {
            return ['status' => -1, 'msg' => '请输入扣除的分享积分'];
        }

        if (!$data['secpwd']) {
            return ['status' => -1, 'msg' => '请输入交易密码'];
        }
        $user = getUserInfo($userId);

        if (webEncrypt($data['secpwd']) != $user['secpwd']) {
            return ['status' => -1, 'msg' => '交易密码错误'];
        }

        $info = [];
        $info['b_uid'] = $userId;
        $info['bank_name'] = $data['bank_name'];
        $info['bank_account'] = $data['bank_account'];
        $info['bank_address'] = $data['bank_address'];
        $info['opening_id'] = $data['opening_id'];
        $info['is_type'] = 1;
        $info['money'] = $data['money'];
        $info['deduct_fen'] = $data['deduct_fen'];

        $res = M('recovery_log')->add($info);
		
      	M('users')->where(['user_id' => $userId])->setDec('invest_money', $money);
      	userAction($userId, '一键回本：总投资金额 - ' . $money);
        M('users_money')->where(['uid' => $userId, 'mid' => 1])->setDec('money', $money);
      
        if ($res) {
            return ['status' => 1, 'msg' => '操作成功'];
        } else {
            return ['status' => -1, 'msg' => '操作失败'];
        }
    }
 
    public function releaseBlock($data) {
        if ($data['id'] > 0) {
            $list = M('block_user_lock')->where(array('id' => $data['id']))->find();
            if ($data['money'] <= 0) {
                $data['money'] = $list['frozen'];
            }
            if ($list['frozen'] < $data['money']) {
                return array('status' => -1, 'msg' => '释放金额错误');
            }
            if ($list['statu'] == 2 && $list['frozen'] > 0 && $data['money'] > 0) {
                $user = getUserInfo($list['uid'], 0);
                $model = new \Think\Model();
                $model->startTrans();
                if ($list['frozen'] == $data['money']) {
                    $userId = M('block_user')->where(array('uid' => $list['uid'], 'mid' => $list['mid']))->setDec('frozen', $list['frozen']);
                    $lockId = M('block_user_lock')->where(array('id' => $list['id']))->save(array('statu' => 1, 'out_time' => time(), 'out_note' => $data['name']));
                    adminLogAdd('释放' . $user['account'] . moneyOne($list['mid'])[name_cn] . $list['frozen']);
                } else {
                    $userId = M('block_user')->where(array('uid' => $list['uid'], 'mid' => $list['mid']))->setDec('frozen', $data['money']);
                    $lockId = M('block_user_lock')->where(array('id' => $list['id']))->save(array('out_time' => time(), 'out_note' => $data['name'], 'frozen' => ($list['frozen'] - $data['money'])));
                    adminLogAdd('释放' . $user['account'] . moneyOne($list['mid'])[name_cn] . $data['money']);
                }
                if ($userId && $lockId) {
                    $model->commit();
                    return array('status' => 1, 'msg' => '操作成功');
                } else {
                    $model->rollback();
                    return array('status' => -1, 'msg' => '操作失败');
                }
            }
        } else {
            return array('status' => -1, 'msg' => '请刷新后重试');
        }
    }

    /**
     * 编辑会员股票数量
     * @param type $data
     * @return type
     */
    public function userBlockEdit($data) {
        if ($data['id'] > 0) {
            if ($data['editType'] <= 0) {
                return array('status' => -1, 'msg' => '请选择调整类型');
            }
            if ($data['money'] <= 0) {
                return array('status' => -1, 'msg' => '请输入要调整的数量');
            }
            $list = M('block_user')->where(array('id' => $data['id']))->find();

            if ($data['isweb'] == 1) {
                if ($data['webname'] == '') {
                    return array('status' => -1, 'msg' => '请输入公司账号');
                }
                $webuser = M('users')->where(array('account' => trim($data['webname'])))->field('user_id')->find();
                if ($webuser['user_id'] <= 0) {
                    return array('status' => -1, 'msg' => '公司账号不存在');
                }
                $weblist = M('block_user')->where(array('uid' => $webuser['user_id'], 'bid' => $list['bid']))->find();
                if ($data['istype'] == 1) {
                    if (($weblist['money'] - $weblist['frozen']) < $data['money']) {
                        return array('status' => -1, 'msg' => $data['webname'] . "当前可用余额不足");
                    }
                }
            }

            $note = $data['note'];
            $A = $B = 1;
            $model = new \Think\Model();
            $model->startTrans();
            $user = M('users')->where(array('user_id' => $list['uid']))->field('account')->find();
            if ($data['istype'] == 1) {
                $note .= '修改' . $user['account'] . blockList($list['sid']) . '账户持用量' . $list['money'] . '为' . ($list['money'] + $data['money']);
                $A = userBlockLogAdd($list['uid'], $list['bid'], $data['money'], $data['editType'], $data['note'], $_SESSION['admin_id'], $comeUid = '');
                if ($data['isweb'] == 1) {
                    $B = userBlockLogAdd($webuser['user_id'], $list['bid'], '-' . $data['money'], $data['editType'], $data['note'], $_SESSION['admin_id'], $list['uid']);
                    $note .= '并由' . $data['webname'] . '支出';
                }
                if ($data['editType'] == 1) {
                    lockUserBlockAdd($list['uid'], $data['money'], $list['bid'], 3, $_SESSION['admin_id'] . $data['note']);
                }
            }
            if ($data['istype'] == 2) {
                $note .= '修改' . $user['account'] . blockList($list['bid']) . '账户持用余额' . $list['money'] . '为' . ($list['money'] - $data['money']);
                $A = userBlockLogAdd($list['uid'], $list['bid'], '-' . $data['money'], $data['editType'], $data['note'], $_SESSION['admin_id']);
                if ($data['isweb'] == 1) {
                    $note .= '并回收至' . $data['webname'];
                    $B = userBlockLogAdd($webuser['user_id'], $list['bid'], $data['money'], $data['editType'], $data['note'], $_SESSION['admin_id'], $list['uid']);
                }
            }
            if ($data['istype'] == 3) {
                $note .= '冻结' . $user['account'] . blockList($list['bid']) . $data['money'];
                if (($list['money'] - $list['frozen']) < $data['money']) {
                    return array('status' => -1, 'msg' => "冻结金额大于当前可用余额" . ($list['money'] - $list['frozen']) . "，请重新输入");
                }
                $A = lockUserBlockAdd($list['uid'], $data['money'], $list['bid'], 4, $_SESSION['admin_id'] . $data['note']);
            }

            $C = adminLogAdd($note);
            if ($A && $B && $C) {
                $model->commit();
                return array('status' => 1, 'msg' => '操作成功');
            } else {
                $model->rollback();
                return array('status' => -1, 'msg' => '操作失败');
            }
        } else {
            return array('status' => -1, 'msg' => '请刷新后重试');
        }
    }

    /**
     * @param $post
     * @param $userId
     * @return array
     */
    public function changeAdd($post, $uid) {

        if ($post['mid']) {
            if ($post['type'] == '') {
                return array('status' => -1, 'msg' => '请输入收款名称');
            }

            if ($post['toMoney'] == '') {
                return array('status' => -1, 'msg' => '请输入收款金额');
            }

            if (empty($post['user_id'])) {
                return array('status' => -1, 'msg' => '网络错误，刷新后重试');
            }

            $toBlockUser = M('block_user')->where(array('bid' => $post['mid'], 'uid' => $post['user_id']))->find();
            $toAccount = M('users')->where(array('user_id' => $toBlockUser['uid']))->field('account')->find();
        } else {
            if (empty($post['toMoney']) || $post['toMoney'] <= 0) {
                return array('status' => -1, 'msg' => '请输入转账数量');
            }
            if (empty($post['address'])) {
                return array('status' => -1, 'msg' => '转账地址不能为空');
            }
            $toBlockUser = M('block_user')->where(array('address' => $post['address']))->find();
            if ($toBlockUser) {
                $toAccount = M('users')->where(array('user_id' => $toBlockUser['uid']))->field('account')->find();
            } else {
                return array('status' => -1, 'msg' => '转账地址错误');
            }
        }

        if ($uid == $toBlockUser['uid']) {
            return array('status' => -1, 'msg' => '不能自己转让自己');
        }
        if (empty($post['secpwd'])) {
            return array('status' => -1, 'msg' => '请输入交易密码');
        }
        $userInfo = M('users')->where(array('user_id' => $uid))->field('secpwd')->find();
        if (webEncrypt($post['secpwd']) != $userInfo['secpwd']) {
            return array('status' => -1, 'msg' => '二级密码 ' . $post['secpwd'] . '验证失败!');
        }
        $info = M('block_change')->where(array('bid' => $toBlockUser['bid']))->find();
        if ($info['fee'] > 0) {
            $poundage = $post['toMoney'] * $info['fee'] / 100;
            if ($info['fee_type'] == 1) {// 扣转出方手续费
                $toMoney = $post['toMoney'] + $poundage;
                $enterMoney = $post['toMoney'];
                $note = $post['note'] . '转出手续费' . $poundage . '%';
            } else {// 扣转入方手续费
                $toMoney = $post['toMoney'];
                $enterMoney = $post['toMoney'] - $poundage;
                $note = $post['note'] . '转入手续费' . $info['fee'] . '%';
            }
        } else {
            $poundage = 0;
            $toMoney = $enterMoney = $post['toMoney'];
            $note = $post['note'];
        }

        if ($toMoney > usersBlock($uid, $toBlockUser['bid'], 3)) {
            return array('status' => -1, 'msg' => blockList($toBlockUser['bid'], 1) . '余额不足');
        }
        $data['uid'] = $uid;
        $data['to_uid'] = $toBlockUser['uid'];
        $data['bid'] = $toBlockUser['bid'];
        $data['type_id'] = $toBlockUser['bid'];
        $data['money'] = $toMoney;
        $data['zf_time'] = time();
        $data['fee'] = $info['fee'] > 0 ? $info['fee'] : 0;
        $data['fee_money'] = $info['fee'] > 0 ? $poundage : 0;
        $data['to_money'] = $enterMoney;
        $data['note'] = $note;
        $data['tishi'] = '转出';
        $data['tishi2'] = '转入';
        $model = new \Think\Model();
        $model->startTrans();
        $A = M('block_change_log')->add($data);
        $B = userBlockLogAdd($uid, $toBlockUser['bid'], '-' . $toMoney, 105, $note, '', $toBlockUser['uid']);
        $C = userBlockLogAdd($toBlockUser['uid'], $toBlockUser['bid'], $enterMoney, 105, $note, '', $toBlockUser['uid']);
        $D = userAction($uid, blockList($toBlockUser['bid'], 1) . '转' . $toMoney . '至' . $toAccount['account'] . $note);
        if ($A && $B && $C && $D) {
            $model->commit();
            return array('status' => 1, 'msg' => '操作成功!');
        } else {
            $model->rollback();
            return array('status' => -1, 'msg' => '操作失败!');
        }
    }

    /**
     * @param $post
     * @param $userId
     * @return array
     */
    public function changeAddEn($post, $uid) {

        if ($post['mid']) {
            if ($post['type'] == '') {
                return array('status' => -1, 'msg' => 'Please enter the name of receipt');
            }

            if ($post['toMoney'] == '') {
                return array('status' => -1, 'msg' => 'Please enter the amount received');
            }

            if (empty($post['user_id'])) {
                return array('status' => -1, 'msg' => 'Network error, try again after refresh');
            }

            $toBlockUser = M('block_user')->where(array('bid' => $post['mid'], 'uid' => $post['user_id']))->find();
            $toAccount = M('users')->where(array('user_id' => $toBlockUser['uid']))->field('account')->find();
        } else {
            if (empty($post['toMoney']) || $post['toMoney'] <= 0) {
                return array('status' => -1, 'msg' => 'Please enter the amount of transfer');
            }
            if (empty($post['address'])) {
                return array('status' => -1, 'msg' => 'The transfer address cannot be empty');
            }
            $toBlockUser = M('block_user')->where(array('address' => $post['address']))->find();
            if ($toBlockUser) {
                $toAccount = M('users')->where(array('user_id' => $toBlockUser['uid']))->field('account')->find();
            } else {
                return array('status' => -1, 'msg' => 'Wrong transfer address');
            }
        }

        if ($uid == $toBlockUser['uid']) {
            return array('status' => -1, 'msg' => 'You can\'t transfer yourself');
        }
        if (empty($post['secpwd'])) {
            return array('status' => -1, 'msg' => 'Please enter your transaction password');
        }
        $userInfo = M('users')->where(array('user_id' => $uid))->field('secpwd')->find();
        if (webEncrypt($post['secpwd']) != $userInfo['secpwd']) {
            return array('status' => -1, 'msg' => 'The secondary password ' . $post['secpwd'] . 'authentication failed!');
        }
        $info = M('block_change')->where(array('bid' => $toBlockUser['bid']))->find();
        if ($info['fee'] > 0) {
            $poundage = $post['toMoney'] * $info['fee'] / 100;
            if ($info['fee_type'] == 1) {// 扣转出方手续费
                $toMoney = $post['toMoney'] + $poundage;
                $enterMoney = $post['toMoney'];
                $note = $post['note'] . '转出手续费' . $poundage . '%';
            } else {// 扣转入方手续费
                $toMoney = $post['toMoney'];
                $enterMoney = $post['toMoney'] - $poundage;
                $note = $post['note'] . '转入手续费' . $info['fee'] . '%';
            }
        } else {
            $poundage = 0;
            $toMoney = $enterMoney = $post['toMoney'];
            $note = $post['note'];
        }

        if ($toMoney > usersBlock($uid, $toBlockUser['bid'], 3)) {
            return array('status' => -1, 'msg' => blockList($toBlockUser['bid'], 1) . 'not sufficient funds');
        }
        $data['uid'] = $uid;
        $data['to_uid'] = $toBlockUser['uid'];
        $data['bid'] = $toBlockUser['bid'];
        $data['type_id'] = $toBlockUser['bid'];
        $data['money'] = $toMoney;
        $data['zf_time'] = time();
        $data['fee'] = $info['fee'] > 0 ? $info['fee'] : 0;
        $data['fee_money'] = $info['fee'] > 0 ? $poundage : 0;
        $data['to_money'] = $enterMoney;
        $data['note'] = $note;
        $data['tishi'] = '转出';
        $data['tishi2'] = '转入';
        $model = new \Think\Model();
        $model->startTrans();
        $A = M('block_change_log')->add($data);
        $B = userBlockLogAdd($uid, $toBlockUser['bid'], '-' . $toMoney, 105, $note, '', $toBlockUser['uid']);
        $C = userBlockLogAdd($toBlockUser['uid'], $toBlockUser['bid'], $enterMoney, 105, $note, '', $toBlockUser['uid']);
        $D = userAction($uid, blockList($toBlockUser['bid'], 1) . '转' . $toMoney . '至' . $toAccount['account'] . $note);
        if ($A && $B && $C && $D) {
            $model->commit();
            return array('status' => 1, 'msg' => 'Successful operation!');
        } else {
            $model->rollback();
            return array('status' => -1, 'msg' => 'operation failure!');
        }
    }

    /**
     * 添加众筹
     * @param type $post
     * @return type
     */
    public function addCrowdConfig($post) {
        if ($post['bid'] <= 0) {
            return array('status' => -1, 'msg' => '请选择货币');
        }
        if ($post['web_taotal'] <= 0) {
            return array('status' => -1, 'msg' => '请输入众筹数量');
        }
        if ($post['start_time'] <= 0) {
            return array('status' => -1, 'msg' => '请设置众筹开始时间');
        }
        $data['bid'] = $post['bid'] ? $post['bid'] : 1;
        $data['web_taotal'] = $post['web_taotal'] ? $post['web_taotal'] : FALSE;
        $data['user_taotal'] = $post['user_taotal'] ? $post['user_taotal'] : 0;
        $data['start_time'] = $post['start_time'] ? strtotime($post['start_time']) : FALSE;
        $data['statu'] = $post['statu'] ? $post['statu'] : 1;
        $data['status'] = $post['status'] ? $post['status'] : 1;
        if ($post['id'] > 0) {
            $resId = M('block_crowd')->where(array('id' => $post['id']))->save($data);
        } else {
            $data['add_time'] = time();
            $resId = M('block_crowd')->add($data);
        }
        if (!$resId) {
            return array('status' => -1, 'msg' => '操作失败');
        } else {
            return array('status' => 1, 'msg' => '操作成功');
        }
    }

    /**
     * 众筹购买
     */
    public function crowdBuy($post, $uid) {
        $crowd = M('block_crowd')->where(['id' => $post['id']])->find();
        // 查出已经购买了的数量
        $kk = M('block_crowd_user')->where(['cid' => $post['id']])->sum('num');

        if ($post['nums'] + $kk > $crowd['web_taotal']) {
            return array('status' => -1, 'msg' => '你最多还可以买' . ($crowd['web_taotal'] - $kk));
        }

        // 查出币的当前价格
        $block = M('block')->where(['id' => $post['bid']])->field('now_price')->find();

        if ($crowd['web_taotal'] < $post['nums']) {
            return ['status' => -1, 'msg' => '众筹数量不足'];
        }

        $block_crowd = M('block_crowd_user')->where(['cid' => $post['id'], 'uid' => $uid])->sum('num');
        if ($post['nums'] > $post['user_taotal']) {
            return array('status' => -1, 'msg' => '每个用户只能购买' . $post['user_taotal']);
        }

        if ($block_crowd >= $post['user_taotal']) {
            return array('status' => -1, 'msg' => '你购买已上限！');
        }

        if ($post['nums'] + $block_crowd > $post['user_taotal']) {
            return array('status' => -1, 'msg' => '你最多还可以买' . ($post['user_taotal'] - $block_crowd));
        }

        if ($crowd['web_taotal'] - $kk <= 0) {
            return array('status' => -1, 'msg' => '数量不足！');
        }

        if ($post['nums'] == '') {
            return array('status' => -1, 'msg' => '请输入购买数量');
        }

        if ($post['toMoney'] == 0) {
            return array('status' => -1, 'msg' => '请输入支付金额');
        }

        if ($post['toMoney'] != $block['now_price'] * $post['nums'] * zfCache('securityInfo.rate_per')) {
            return ['status' => -1, 'msg' => '支付金额错误'];
        }

        if (usersMoney($uid, 2, 3) < $post['toMoney']) {
            return array('status' => -1, 'msg' => '矿金余额不足，请充值后重试');
        }


        $data['cid'] = $post['id'];
        $data['bid'] = $post['bid'];
        $data['uid'] = $uid;
        $data['zf_time'] = time();
        $data['num'] = $post['nums'];
        $data['price'] = $block['now_price'];
        $data['total'] = $post['toMoney'];
        $data['status'] = 9;

        $model = new \Think\Model();
        $model->startTrans();

        $A = M('block_crowd_user')->add($data);
        $B = userMoneyLogAdd($uid, 2, '-' . $post['toMoney'], 55, '购买众筹');
        $C = userBlockLogAdd($uid, 1, $post['nums'], 8, '矿金购买众筹');

//        $D = M('block_crowd')->where(['id' => $post['id']])->setDec('web_taotal', $post['num']);

        if ($A && $B && $C) {
            $this->commit();
            return array('status' => 1, 'msg' => '操作成功');
        } else {
            return array('status' => -1, 'msg' => '操作失败');
        }
    }

    /**
     * 众筹购买
     */
    public function crowdBuyEn($post, $uid) {
        $crowd = M('block_crowd')->where(['id' => $post['id']])->find();
        // 查出已经购买了的数量
        $kk = M('block_crowd_user')->where(['cid' => $post['id']])->sum('num');

        if ($post['nums'] + $kk > $crowd['web_taotal']) {
            return array('status' => -1, 'msg' => 'You can buy it at most' . ($crowd['web_taotal'] - $kk));
        }

        // 查出币的当前价格
        $block = M('block')->where(['id' => $post['bid']])->field('now_price')->find();

        if ($crowd['web_taotal'] < $post['nums']) {
            return ['status' => -1, 'msg' => 'The number of crowdfunding is insufficient'];
        }

        $block_crowd = M('block_crowd_user')->where(['cid' => $post['id'], 'uid' => $uid])->sum('num');
        if ($post['nums'] > $post['user_taotal']) {
            return array('status' => -1, 'msg' => 'Each user can only buy' . $post['user_taotal']);
        }

        if ($block_crowd >= $post['user_taotal']) {
            return array('status' => -1, 'msg' => 'Your purchase is capped！');
        }

        if ($post['nums'] + $block_crowd > $post['user_taotal']) {
            return array('status' => -1, 'msg' => 'You can buy it at most' . ($post['user_taotal'] - $block_crowd));
        }

        if ($crowd['web_taotal'] - $kk <= 0) {
            return array('status' => -1, 'msg' => 'lazy weight！');
        }

        if ($post['nums'] == '') {
            return array('status' => -1, 'msg' => 'Please enter the purchase quantity');
        }

        if ($post['toMoney'] == 0) {
            return array('status' => -1, 'msg' => 'Please enter the amount');
        }

        if ($post['toMoney'] != $block['now_price'] * $post['nums'] * zfCache('securityInfo.rate_per')) {
            return ['status' => -1, 'msg' => 'Payment amount error'];
        }

        if (usersMoney($uid, 2, 3) < $post['toMoney']) {
            return array('status' => -1, 'msg' => 'The balance of mineral gold is insufficient, please retry after recharging');
        }


        $data['cid'] = $post['id'];
        $data['bid'] = $post['bid'];
        $data['uid'] = $uid;
        $data['zf_time'] = time();
        $data['num'] = $post['nums'];
        $data['price'] = $block['now_price'];
        $data['total'] = $post['toMoney'];
        $data['status'] = 9;

        $model = new \Think\Model();
        $model->startTrans();

        $A = M('block_crowd_user')->add($data);
        $B = userMoneyLogAdd($uid, 2, '-' . $post['toMoney'], 55, '购买众筹');
        $C = userBlockLogAdd($uid, 1, $post['nums'], 8, '矿金购买众筹');

//        $D = M('block_crowd')->where(['id' => $post['id']])->setDec('web_taotal', $post['num']);

        if ($A && $B && $C) {
            $this->commit();
            return array('status' => 1, 'msg' => 'operate successfully');
        } else {
            return array('status' => -1, 'msg' => 'operation failure');
        }
    }

    public function depositInfo($post, $uid) {
        $post['money'] = (float) $post['money'];
        $user = getUserInfo($uid);
        if (webEncrypt($post['secpwd']) != $user['secpwd']) {
            return array('status' => -1, 'msg' => '交易密码 ' . $post['secpwd'] . '验证失败!');
        }

        if ($post['money'] <= 0) {
            return array('status' => -1, 'msg' => '操作失败!');
        }

        $userBlock = M('block_user')->where(['uid' => $uid])->find();


        if ($post['money'] > $userBlock['money']) {
            return array('status' => -1, 'msg' => '余额不足');
        }

        $model = new \Think\Model();
        $model->startTrans();
        $A = userBlockLogAdd($uid, $userBlock['bid'], '-' . $post['money'], 110);
        $D = userAction($uid, '储存' . blockList($userBlock['bid'], 1));
		$userMoney = $post['money'] * blockList(1, 2);
        $B = M('block_user')->where(['uid' => $uid])->setInc('deposit', $post['money']);
        M('users')->where(['user_id' => $uid])->setInc('invest_money', $userMoney);
      	userAction($uid, '总投资金额 + ' . $userMoney);
      
      	$cunchu = [];
        $cunchu['uid'] = $uid;
        $cunchu['jifen'] = blockList(1, 2) * $post['money'];
        $cunchu['num'] = $post['money'];
        $cunchu['per'] = blockList(1, 2);
        $cunchu['add_time'] = time();
        $cunchu['is_type'] = 2;

        M('chucun_log')->add($cunchu);
      
        if ($A && $B && $D) {
            $model->commit();
          	levelS($uid);
          	if ($user['tjr_id'] > 0) {
                bonus1Clear($user['tjr_id'], $uid, $userMoney, '存储数量' . $post['money']);
            }
            return array('status' => 1, 'msg' => '操作成功!');
        } else {
            $model->rollback();
            return array('status' => -1, 'msg' => '操作失败!');
        }
    }
	
  	/**
     * 管理员调整会员的 存储数量
     * @param array $post
     * @param type $uid
     * @return type
     */
    public function rechargeBlock($post) {
        $post['money'] = (float) $post['name'];
        if ($post['money'] <= 0) {
            return array('status' => -1, 'msg' => '操作失败!');
        }

        $userBlock = M('block_user')->where(['uid' => $post['id']])->find();
        if ($post['money'] > $userBlock['money']) {
            return array('status' => -1, 'msg' => '余额不足');
        }
        $user = getUserInfo($userBlock['uid']);
        $model = new \Think\Model();
        $model->startTrans();
        $A = userBlockLogAdd($userBlock['uid'], $userBlock['bid'], '-' . $post['money'], 110);
        $B = M('block_user')->where(['uid' => $userBlock['uid'], 'bid' => 1])->setInc('deposit', $post['money']);
        $D = adminLogAdd('给' . $user['account'] . '存储' . $post['money']);
        if ($A && $B && $D) {
            $model->commit();
          	levelS($userBlock['uid']);
        /**    if ($user['tjr_id'] > 0) {
              	$userMoney = $post['money'] * blockList(1, 2);
                bonus1Clear($user['tjr_id'], $userBlock['uid'], $userMoney, '存储数量' . $post['money']);
            }
            */
            return array('status' => 1, 'msg' => '操作成功!');
        } else {
            $model->rollback();
            return array('status' => -1, 'msg' => '操作失败!');
        }
    }

}
