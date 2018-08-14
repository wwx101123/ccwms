<?php

namespace Zfuwl\Logic;

use Think\Model\RelationModel;

class RechargeLogic extends RelationModel
{

    protected $tableName = 'pay_config';

    public function payInfo($post)
    {
        $num = D('pay_config')->where(array('mid' => $post['mid']))->count();
        if ($post['id'] > 0) {
            if ($num > 1) {
                return array('status' => -1, 'msg' => '己存在相同规则');
            }
        } else {
            if ($num > 0) {
                return array('status' => -1, 'msg' => '己存在相同规则');
            }
        }
        $post['mid'] && $data['mid'] = $post['mid'];
        $post['low'] && $data['low'] = $post['low'];
        $post['bei'] && $data['bei'] = $post['bei'];
        $post['out'] && $data['out'] = $post['out'];
        $post['statu'] && $data['statu'] = $post['statu'];
        if ($post['id'] > 0) {
            $resId = M('pay_config')->where(array('id' => $post['id']))->save($data);
        } else {
            $resId = M('pay_config')->add($data);
        }
        if (!$resId) {
            return array('status' => -1, 'msg' => '添加失败');
        } else {
            return array('status' => 1, 'msg' => '添加成功');
        }
    }

    /**
     * 汇款充值
     * @param array $data 会员提交的数据
     * @param int $userId 会员id
     * @return array 返回结果
     */
    public function remitRecharge($data, $userId)
    {
        # 会员信息
        $user = D('UserView')->where(array('user_id' => $userId))->field('user_id,secpwd')->find();
        if (!$user) {
            return array('status' => -1, 'msg' => '请先登录');
        }
        if (webEncrypt($data['secpwd']) != $user['secpwd']) {
            return array('status' => -1, 'msg' => '二级密码验证失败');
        }
        $moneyId = intval($data['money_id']);
        if ($moneyId <= 0) {
            return array('status' => -1, 'msg' => '请选择要充值的钱包');
        }
        $bankId = intval($data['bank_id']);
        if ($bankId <= 0) {
            return array('status' => -1, 'msg' => '请选择汇款银行');
        }
        $price = floatval($data['price']);
        if ($price <= 0) {
            return array('status' => -1, 'msg' => '请输入汇款金额');
        }
        if ($data['img'] == '') {
            return array('status' => -1, 'msg' => '请上传汇款截图');
        }

        $per = moneyList($moneyId, 8);
        $data = array(
            'uid' => $userId,
            'bank_id' => $bankId,
            'mid' => $moneyId,
            'add_time' => time(),
            'add_money' => $price,
            'money_per' => $per,
            'actual_money' => $price / $per,
            'fk_time' => strtotime($data['fk_time']),
            'img' => $data['img'],
            'type' => 2,
            'note' => $data['note']
        );

        $res = M('users_money_add')->add($data);
        if ($res) {
            return array('status' => 1, 'msg' => '提交成功');
        } else {
            return array('status' => -1, 'msg' => '提交失败');
        }
    }

    /**
     * 在线充值
     * @param array $post 会员提交的数据
     * @param int $userId 会员id
     * @return array 返回结果
     */
    public function rechargeAdd($post, $userId)
    {
        $user = D("UserView")->where(array('user_id' => $userId))->field('user_id')->find();
        if(!$user) {
            return array('status' => -1, 'msg' => '请先登陆');
        }
        $moneyId = intval($post['money_id']);
        if($moneyId <= 0) {
            return array('status' => -1, 'msg' => '请选择钱包');
        }
        $price = floatval($post['price']);
        if($price <= 0) {
            return array('status' => -1, 'msg' => '请输入充值金额');
        }

        $info = M('pay_config')->where(array('mid' => $moneyId, 'statu' => 1))->find();
        if(!$info) {
            return array('status' => -1, 'msg' => '请选择钱包');
        }
        if($price < $info['low'] || $price%$info['bei'] != 0 ||$price > $info['out']) {
            return array('status' => -1, 'msg' => moneyList($moneyId).'充值金额最低'.$info['low'].'并且是'.$info['out'].'倍数最多'.$info['out']);
        }

        $per = moneyList($moneyId, 8);
        $data = array(
            'uid' => $userId,
            'mid' => $moneyId,
            'order_sn' => '74_'.date('YmdHis').rand(10000,99999),
            'add' => $price,
            'per' => $per,
            'money' => $price*$per,
            'add_time' => time()
        );

        $res = M('pay_recharge')->add($data);
        if($res) {
            return array('status' => 1, 'msg' => '提交成功', 'url' => U('Recharge/locationRecharge', array('id' => $res)));
        } else {
            return array('status' => -1, 'msg' => '提交失败');
        }
    }
}
