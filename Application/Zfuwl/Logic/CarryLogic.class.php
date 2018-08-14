<?php

namespace Zfuwl\Logic;

use Think\Model\RelationModel;

class CarryLogic extends RelationModel {

    protected $tableName = 'money_carry';

    public function carryInfo($post) {
        if ($post['mid'] <= 0) {
            return array('status' => -1, 'msg' => '请选择钱包');
        }
        if ($post['is_tk'] <= 0) {
            return array('status' => -1, 'msg' => '请选择提现方案');
        }
        if ($post['is_tk'] == 2) {
            if ($post['week_time'] == '') {
                return array('status' => -1, 'msg' => '请选择每周可以提现的日期');
            }
        }
        if ($post['is_tk'] == 3) {
            if ($post['month_time'] == '') {
                return array('status' => -1, 'msg' => '请选择每月可以提现的日期');
            }
        }
        $num = D('money_carry')->where(array('money_id' => $post['money_id'], 'level_id' => $post['level_id']))->count();
        if ($post['id'] > 0) {
            if ($num > 1) {
                return array('status' => -1, 'msg' => '己存在相同规则');
            }
        } else {
            if ($num > 0) {
                return array('status' => -1, 'msg' => '己存在相同规则');
            }
        }
        $post['low'] && $data['low'] = $post['low'];
        $post['bei'] && $data['bei'] = $post['bei'];
        $post['out'] && $data['out'] = $post['out'];
        $post['fee'] && $data['fee'] = $post['fee'];
        $post['total_fee'] && $data['total_fee'] = $post['total_fee'];
        $post['mid'] && $data['mid'] = $post['mid'];
        $post['level_id'] && $data['level_id'] = $post['level_id'];
        $post['is_tk'] && $data['is_tk'] = $post['is_tk'];
        $post['add_time'] && $data['add_time'] = $post['add_time'];
        $post['out_time'] && $data['out_time'] = $post['out_time'];
        $post['week_time'] && $data['week_time'] = implode(',', $post['week_time']);
        $post['month_time'] && $data['month_time'] = implode(',', $post['month_time']);
        $post['day_total'] && $data['day_total'] = $post['day_total'];
        $post['statu'] && $data['statu'] = $post['statu'];
        if ($post['id'] > 0) {
            $resId = M('money_carry')->where(array('id' => $post['id']))->save($data);
        } else {
            $resId = M('money_carry')->add($data);
        }
        if (!$resId) {
            return array('status' => -1, 'msg' => '操作失败');
        } else {
            return array('status' => 1, 'msg' => '操作成功');
        }
    }

    /**
     * 提现
     * @param array $post 会员提交的数据
     * @param int $uid 会员id
     * @return array 操作状态
     */
    public function carryAdd($post, $uid) {
        $user = D('UserView')->where(array('user_id' => $uid))->field('user_id,opening_id,bank_account,bank_name,bank_address,secpwd,level')->find();
        if (!$user) {
            return array('status' => -1, 'msg' => '请先登陆');
        }
        if ($post['secpwd'] == '') {
            return array('status' => -1, 'msg' => '请输入二级密码');
        }
        if (webEncrypt($post['secpwd']) != $user['secpwd']) {
            return array('status' => -1, 'msg' => '二级密码验证失败');
        }
        $post['money'] = floatval($post['money']);
        if ($post['money'] <= 0) {
            return array('status' => -1, 'msg' => '请输入提现金额');
        }
        $carryInfo = M('money_carry')->where(array('mid' => $post['mid'], 'statu' => 1))->find();
        if (!$carryInfo) {
            return array('status' => -1, 'msg' => '暂时不能提现');
        }
        $usersMoney = usersMoney($uid, $post['mid']);
        if ($post['money'] > $usersMoney) {
            return array('status' => -1, 'msg' => moneyList($post['mid']) . '余额不足');
        }
        $level = levelInfo($user['level']);
        if($post['mid'] == 4) {
            $low = floatval($level['tx_bei_money4']);
            $bei = floatval($level['tx_bei_money4']);
        } else {
            $low = floatval($level['tx_bei']);
            $bei = floatval($level['tx_bei']);
        }
        if ($post['money'] < $low || $post['money'] % $bei != 0) {
            return array('status' => -1, 'msg' => '提现最低' . $low . '并且是' . $bei. '的倍数');
        }
        # 2周提 3月提
        switch ($carryInfo['is_tk']) {
            case 2:
                if (!stristr($carryInfo['week_time'], date('w'))) {
                    return array('status' => -1, 'msg' => '今日不能提现');
                }
                break;
            case 3:
                if ($carryInfo['month_time']) {
                    $carryInfo['month_time'] = explode(',', $carryInfo['month_time']);
                    if (!in_array(date('d'), $carryInfo['month_time'])) {
                        return array('status' => -1, 'msg' => '今日不能兑换');
                    }
                }
                break;
        }
        if (date('H') < $carryInfo['add_time'] || date('H') > $carryInfo['out_time']) {
            return array('status' => -1, 'msg' => "提现时间为" . $carryInfo['add_time'] . "点-" . $carryInfo['out_time'] . "点");
        }
        # 今日提现封顶
        if ($carryInfo['day_total'] > 0) {
            $dayCarrySum = M('money_carry_log')->where(array('uid' => $uid, 'mid' => $post['mid'], 'statu' => array('neq', 3)))->sum('add_money');

            if ($dayCarrySum < $carryInfo['day_total']) {
                if ($post['money'] + $dayCarrySum > $carryInfo['day_total']) {
                    $post['money'] = $carryInfo['day_total'] - $dayCarrySum;
                    return array('status' => -1, 'msg' => '今日最多还能提现' . $post['money'] . '元');
                }
            } else {
                return array('status' => -1, 'msg' => '今日兑换己达最高允许额度，请明日再试');
            }
        }

        # 手续费
        $sxf = $post['money'] * $carryInfo['fee'] / 100;
        $sjSxf = 0;
        if ($carryInfo['total_fee'] > 0) {
            if ($sxf > $carryInfo['total_fee']) {
                $sjSxf = $carryInfo['total_fee'] - $sxf;
            } else {
                $sjSxf = $sxf;
            }
        }

        if ($user['opening_id'] > 0 && $user['bank_account'] > 0 && $user['bank_name'] != '') {
            $data = array(
                'uid' => $uid,
                'mid' => $post['mid'],
                'add_time' => time(),
                'add_money' => $post['money'],
                'fee' => $carryInfo['fee'],
                'fee_money' => $sjSxf,
                'out_money' => $post['money'] - $sjSxf,
                'opening_id' => $user['opening_id'],
                'bank_address' => $user['bank_address'],
                'bank_account' => $user['bank_account'],
                'bank_name' => $user['bank_name'],
                'statu' => 2
            );
            $res = M('money_carry_log')->add($data);
            if ($res) {
                userMoneyLogAdd($uid, $post['mid'], '-' . $post['money'], 104, '申请提现');
                return array('status' => 1, 'msg' => '提现申请成功');
            } else {
                return array('status' => -1, 'msg' => '提现申请失败');
            }
        } else {
            return array('status' => -1, 'msg' => '先完善银行信息');
        }
    }

    public function listCarryAdd($post) {
        if ($post['mid'] <= 0) {
            return array('status' => -1, 'msg' => '请选择钱包');
        }
        if ($post['istype'] <= 0) {
            return array('status' => -1, 'msg' => '请选择提现方案');
        }
        if ($post['money'] <= 0) {
            return array('status' => -1, 'msg' => '请输入操作金额');
        }
        if ($post['istype'] == 3) {
            if ($post['money'] >= 100) {
                return array('status' => -1, 'msg' => '按账户比例，最大值不可超过 100 %');
            }
        }
        $resId = listCarryAdd($post['mid'], $post['istype'], $post['money'], $post['fee']);
        if (!$resId) {
            return array('status' => -1, 'msg' => '操作失败');
        } else {
            return array('status' => 1, 'msg' => '操作成功');
        }
    }

}
