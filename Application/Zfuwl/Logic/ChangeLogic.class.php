<?php

namespace Zfuwl\Logic;

use Think\Model\RelationModel;

class ChangeLogic extends RelationModel
{

    protected $tableName = 'money_change';

    public function changeInfo($post)
    {
        $num = D('money_change')->where(['money_id' => $post['money_id'], 'type_id' => $post['type_id']])->count();
        if ($post['id'] > 0) {
            if ($num > 1) {
                return ['status' => -1, 'msg' => '已存在相同规则'];
            }
        } else {
            if ($num > 0) {
                return ['status' => -1, 'msg' => '己存在相同规则'];
            }
        }
        if ($post['fee'] > 0) {
            if ($post['fee_type'] <= 0) {
                return ['status' => -1, 'msg' => '请选择手续费激纳方'];
            }
        }
        $data = [
            'low' => floatval($post['low'])
            ,'bei' => floatval($post['bei'])
            ,'out' => floatval($post['out'])
            ,'fee_type' => intval($post['fee_type'])
            ,'fee' => floatval($post['fee'])
            ,'money_id' => intval($post['money_id'])
            ,'type_id' => intval($post['type_id'])
            ,'per' => floatval($post['per'])
            ,'is_upper' => intval($post['is_upper']) == 1 ? 1 : 2
            ,'is_lower' => intval($post['is_lower']) == 1 ? 1 : 2
            ,'is_above' => intval($post['is_above']) == 1 ? 1 : 2
            ,'is_below' => intval($post['is_below']) == 1 ? 1 : 2
            ,'is_agent' => intval($post['is_agent']) == 1 ? 1 : 2
            ,'statu' => intval($post['statu']) == 1 ? 1 : 2
            ,'user_to_agent_low' => floatval($post['user_to_agent_low'])
            ,'user_to_agent_bei' => floatval($post['user_to_agent_bei'])
            ,'user_to_agent_day_out' => floatval($post['user_to_agent_day_out'])
            ,'user_to_agent_fee' => floatval($post['user_to_agent_fee'])
            ,'agent_to_agent_low' => floatval($post['agent_to_agent_low'])
            ,'agent_to_agent_bei' => floatval($post['agent_to_agent_bei'])
            ,'agent_to_agent_day_out' => floatval($post['agent_to_agent_day_out'])
            ,'agent_to_agent_fee' => floatval($post['agent_to_agent_fee'])
        ];
        if ($post['id'] > 0) {
            $resId = M('money_change')->where(['id' => $post['id']])->save($data);
        } else {
            $resId = M('money_change')->add($data);
        }
        if (!$resId) {
            return ['status' => -1, 'msg' => '添加失败'];
        } else {
            return ['status' => 1, 'msg' => '添加成功'];
        }
    }

    /**
     * @param $post
     * @param $userId
     * @return array
     */
    public function changeAdd($post, $userId)
    {
        $toAccount = getUserInfo($post['toAccount'], 3);
        if ($userId == $toAccount['user_id']) {
            return ['status' => -1, 'msg' => '不能自己转自己'];
        }
        if (!$toAccount) {
            return ['status' => -1, 'msg' => $post['toAccount'] . '账号不存在'];
        }
        if (intVal($post['money_id']) <= 0) {
            return ['status' => -1, 'msg' => '请选择转账钱包'];
        }

        $user = userInfo($userId);
        if (webEncrypt($post['secpwd']) != $user['secpwd']) {
            return ['status' => -1, 'msg' => '二级密码验证失败'];
        }

//        if (trim($post['number']) != trim(dataInfo($user['data_id'])[number])) {
//            return array('status' => -1, 'msg' => '证件输入错误，请重新输入');
//        }
        $info = M('money_change')->where(array('money_id' => $post['money_id'], 'statu' => 1))->find();
        if (!$info) {
            return ['status' => -1, 'msg' => '操作失败'];
        }

        $low = $dayOut = $bei = $fee = 0;
//        if($user['agent'] > 0 && $toAccount['agent'] > 0) {
//            $low = floatval($info['agent_to_agent_low']);
//            $dayOut = floatval($info['agent_to_agent_day_out']);
//            $bei = floatval($info['agent_to_agent_bei']);
//            $fee = floatval($info['agent_to_agent_fee']);
//            $changeType = 3;
//        } elseif ($toAccount['agent'] > 0) {
//            $low = floatval($info['user_to_agent_low']);
//            $dayOut = floatval($info['user_to_agent_day_out']);
//            $bei = floatval($info['user_to_agent_bei']);
//            $fee = floatval($info['user_to_agent_fee']);
//            $changeType = 2;
//        } else {
            $low = floatval($info['low']);
            $bei = floatval($info['bei']);
            $fee = floatval($info['fee']);
            $changeType = 1;
//        }


        if ($post['toMoney'] < $low || $post['toMoney'] % $bei != 0) {
            return ['status' => -1, 'msg' => '金额输入错误，转账' . $low . '起且是' . $bei . '倍数!'];
        }
        if($dayOut > 0) {
            $daydChangeTotal = floatval(M('money_change_log')->where(['uid' => $user['user_id'], 'change_type' => $changeType])->sum('money'));
            if($daydChangeTotal + $post['toMoney'] > $dayOut) {
                $allowChangeMoney = $dayOut-$daydChangeTotal;
                $allowChangeMoney = ($allowChangeMoney > 0 ? $allowChangeMoney : 0);
                return ['status' => -1, 'msg' => '今天最多还能转'.$allowChangeMoney];
            }
        }

        if ($fee > 0) {
            $poundage = $post['toMoney'] * $fee / 100;
            if ($info['fee_type'] == 1) {// 扣转出方手续费
                $toMoney = $post['toMoney'] + $poundage;
                $enterMoney = $post['toMoney'];
                $toNote = ',' . $post['toMoney'] . '，转出手续费' . $poundage . '%';
            } else {// 扣转入方手续费
                $toMoney = $post['toMoney'];
                $enterMoney = $post['toMoney'] - $poundage;
                $enterNote = $post['toMoney'] . '，转入手续费' . $poundage . '%';
            }
        } else {
            $toMoney = $enterMoney = $post['toMoney'];
        }

        if ($toMoney > usersMoney($userId, $post['money_id'], 1)) {
            return array('status' => -1, 'msg' => moneyList($post['money_id'], 1) . '余额不足');
        }

        $changeTjrId = $changeZdrId = $Goonline = $Offline = 0;
        // 只能向上级转
        if ($info['is_upper'] == 1) {
            $arr = prevtd($userId);
            if (!in_array($toAccount['user_id'], $arr)) {
                $changeTjrId = 1;
            }
        }
        // 只能向下级转
        if ($info['is_lower'] == 1) {
            global $arr;
            $arr = array();
            $arr = nexttd($userId);
            if (!in_array($toAccount['user_id'], $arr)) {
                $changeZdrId = 1;
            }
        }

        if ($changeTjrId == 1 && $changeZdrId == 1) {
            return array('status' => -1, 'msg' => '必须是上级或者下级会员才可转让');
        }
        // 只能向上线转
        if ($info['is_above'] == 1) {
            $userBranch = M('users_branch')->where(array('uid' => $userId))->field('path')->find();
            $userList = explode(',', $userBranch['path']);
            if (!in_array($toAccount['user_id'], $userList)) {
                $Goonline = 1;
            }
        }
        // 只能向下线转
        if ($info['is_below'] == 1) {
            $userBranch = M('users_branch')->where(array('uid' => $userId))->find();
            $userList = M('users_branch')->where(array('path' => array("like", "%" . $userBranch['path'] . ',' . $userBranch['user_id'] . "%")))->getField('branch_id, uid');
            if (!in_array($toAccount['user_id'], $userList)) {
                $Offline = 1;
            }
        }
        if ($Goonline == 1 && $Offline == 1) {
            return array('status' => -1, 'msg' => '必须是上线或者下线会员才可转让');
        }
        
        $data = [
            'uid' => $userId
            ,'to_uid' => $toAccount['user_id']
            ,'mid' => $post['money_id']
            ,'type_id' => $post['money_id']
            ,'money' => $toMoney
            ,'zf_time' => time()
            ,'fee_money' => $fee
            ,'poundage' => $poundage
            ,'to_money' => $enterMoney
            ,'note' => $post['note'].$toNote.$enterNote
            ,'change_type' => $changeType
        ];


        // $data['uid'] = $userId;
        // $data['to_uid'] = $toAccount['user_id'];
        // $data['mid'] = $post['money_id'];
        // $data['type_id'] = $post['money_id'];
        // $data['money'] = $toMoney;
        // $data['zf_time'] = time();
        // $data['poundage'] = $info['fee'] ? $info['fee'] : 0;
        // $data['type_money'] = $enterMoney;
        // $data['note'] = $post['note'] . $toNote . $enterNote;
        $model = new \Think\Model();
        $model->startTrans();
        $A = M('money_change_log')->add($data);
        $B = userMoneyLogAdd($userId, $post['money_id'], '-' . $toMoney,105, '转至' . $post['toAccount'] . $toNote, '', $toAccount['user_id']);
        $C = userMoneyLogAdd($toAccount['user_id'], $info['type_id'], $enterMoney,105, $user['account'] . '转入' . $enterNote, '', $userId);
        $D = userAction($userId, moneyList($post['money_id']) . '转' . $toMoney . '至' . $post['toAccount'] . $toNote);
        if ($A && $B && $C && $D) {
            $model->commit();
            return array('status' => 1, 'msg' => '操作成功!');
        } else {
            $model->rollback();
            return array('status' => -1, 'msg' => '操作失败!');
        }
    }

}
