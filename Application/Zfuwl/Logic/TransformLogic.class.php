<?php

namespace Zfuwl\Logic;

use Think\Model\RelationModel;

class TransformLogic extends RelationModel {

    protected $tableName = 'money_transform';

    public function transFormInfo($post) {
        if ($post['money_id'] <= 0) {
            return array('status' => -1, 'msg' => '请选择转出钱包');
        }
        if ($post['type_id'] <= 0) {
            return array('status' => -1, 'msg' => '请选择转入钱包');
        }
        if ($post['money_id'] == $post['type_id']) {
            return array('status' => -1, 'msg' => '转出钱包不能与转入钱包相同');
        } else {
            $num = D('money_transform')->where(array('money_id' => $post['money_id'], 'type_id' => $post['type_id']))->count();
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
            $post['type_id'] && $data['type_id'] = $post['type_id'];
            $post['low'] && $data['low'] = $post['low'];
            $data['bei'] = $post['bei'] > 0 ? $post['bei'] : 0;
            $data['out'] = $post['out'] > 0 ? $post['out'] : 0;
            $data['fee'] = $post['fee'] > 0 ? $post['fee'] : 0;
            $post['statu'] && $data['statu'] = $post['statu'];
            if ($post['id'] > 0) {
                $resId = M('money_transform')->where(array('id' => $post['id']))->save($data);
            } else {
                $resId = M('money_transform')->add($data);
            }
            if (!$resId) {
                return array('status' => -1, 'msg' => '添加失败');
            } else {
                return array('status' => 1, 'msg' => '添加成功');
            }
        }
    }

    public function transFormAdd($post, $user_id) {
        $user = getUserInfo($user_id);
        if (webEncrypt($post['secpwd']) != $user['secpwd']) {
            return array('status' => -1, 'msg' => '二级密码 ' . $post['secpwd'] . '验证失败!');
        }
        $post['money_id'] = intval($post['money_id']);
        $post['type_id'] = intval($post['type_id']);
        $post['toMoney'] = floatval($post['toMoney']);
        if ($post['money_id'] <= 0) {
            return array('status' => -1, 'msg' => '请选择转出钱包');
        }
        if ($post['type_id'] <= 0) {
            return array('status' => -1, 'msg' => '请选择转入钱包');
        }
        $info = M('money_transform')->where(array('money_id' => $post['money_id'], 'type_id' => $post['type_id'], 'statu' => 1))->find();
        if (!$info) {
            return array('status' => -1, 'msg' => '操作失败!');
        }
        if ($post['toMoney'] <= 0) {
            return array('status' => -1, 'msg' => '请输入转换金额');
        }
        if ($post['toMoney'] < $info['low'] || $post['toMoney'] % $info['bei'] != 0) {
            return array('status' => -1, 'msg' => '金额输入错误，转让' . $info['low'] . '起且是' . $info['bei'] . '倍数!');
        }
        if ($info['fee'] > 0) {
            $poundage = $post['toMoney'] * $info['fee'] / 100;
            $enterMoney = $post['toMoney'] - $poundage;
            $toMoney = $post['toMoney'];
            $enterNote = $post['note'] . ',' . $post['toMoney'] . '手续费' . $poundage;
        } else {
            $toMoney = $enterMoney = $post['toMoney'];
            $enterNote = $post['note'];
        }

        if ($toMoney > usersMoney($user_id, $post['money_id'], 1)) {
            return array('status' => -1, 'msg' => moneyList($post['money_id'], 1) . '余额不足!');
        }
        $data = [
            'uid' => $user_id,
            'mid' => $post['money_id'],
            'type_id' => $post['type_id'],
            'money' => $toMoney,
            'zf_time' => time(),
            'poundage' => $info['poundage'] ? $info['poundage'] : 0,
            'type_money' => $enterMoney * $info['per'],
            'note' => $post['note']
        ];
        $model = new \Think\Model();
        $model->startTrans();
        $A = M('money_transform_log')->add($data);
        $B = userMoneyLogAdd($user_id, $post['money_id'], '-' . $toMoney, 108, '转出至' . moneyList($post['type_id']) . $enterNote);
        $C = userMoneyLogAdd($user_id, $post['type_id'], $data['type_money'], 108, moneyList($post['money_id']) . '转入' . $enterNote);
        $D = userAction($user_id, moneyList($post['money_id']) . '转' . $toMoney . '至' . moneyList($post['type_id']));
        if ($A && $B && $C && $D) {
            $model->commit();
            return array('status' => 1, 'msg' => '转换成功!');
        } else {
            $model->rollback();
            return array('status' => -1, 'msg' => '转换失败!');
        }
    }

    /**
     * 添加 修改参数
     */
    public function mutualSwitchAdd($post, $user_id) {

        $post1 = floatval($post['toMoney']);
        if ($post1 <= 0) {
            return array('status' => -1, 'msg' => '数量不能为空');
        }
        $user = getUserInfo($user_id);
        if (webEncrypt($post['secpwd']) != $user['secpwd']) {
            return array('status' => -1, 'msg' => '二级密码 ' . $post['secpwd'] . '验证失败!');
        }

        $where = array(
            'money_id' => $post['money_id'],
            'type_id' => $post['type_id'],
            'is_type' => 1
        );
        $info = M('money_transform')->where($where)->find();

        if (!$info) {
            return array('status' => -1, 'msg' => '操作失败!');
        }
        if ($post['toMoney'] < $info['low'] || $post['toMoney'] % $info['bei'] != 0) {
            return array('status' => -1, 'msg' => '金额输入错误，转让' . $info['low'] . '起且是' . $info['bei'] . '倍数!');
        }

        if ($info['fee'] > 0) {
            $poundage = $post['toMoney'] * $info['fee'] / 100;
            $enterMoney = $post['toMoney'] - $poundage;
            $toMoney = $post['toMoney'];
            $enterNote = $post['note'] . ',' . $post['toMoney'] . '手续费' . $poundage;
        } else {
            $toMoney = $enterMoney = $post['toMoney'];
            $enterNote = $post['note'];
        }

        if ($toMoney > usersMoney($user_id, $post['money_id'], 1)) {
            return array('status' => -1, 'msg' => moneyList($post['money_id'], 1) . '余额不足!');
        }
        $data['user_id'] = $user_id;
        $data['money_id'] = $post['money_id'];
        $data['type_id'] = $post['type_id'];
        $data['money'] = $toMoney;
        $data['zf_time'] = time();
        $data['poundage'] = $info['poundage'] ? $info['poundage'] : 0;
        $data['type_money'] = $enterMoney * $info['per'];

        $data['note'] = $post['note'];
        $model = new \Think\Model();
        $model->startTrans();
        $A = M('money_transform_log')->add($data);
        $B = userMoneyAddLog($user_id, $post['money_id'], '-' . $toMoney, 0, 108, '转出至' . moneyList($post['type_id']) . $enterNote);
        $C = userMoneyAddLog($user_id, $post['type_id'], $data['type_money'], 0, 108, moneyList($post['money_id']) . '转入' . $enterNote);
        $D = userLogAdd($user_id, moneyList($post['money_id']) . '转' . $toMoney . '至' . moneyList($post['type_id']));
        if ($A && $B && $C && $D) {
            $model->commit();
            return array('status' => 1, 'msg' => '转换成功!');
        } else {
            $model->rollback();
            return array('status' => -1, 'msg' => '转换失败!');
        }
    }

}
