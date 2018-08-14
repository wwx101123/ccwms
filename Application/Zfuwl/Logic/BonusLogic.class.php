<?php

namespace Zfuwl\Logic;

use Think\Model\RelationModel;

class BonusLogic extends RelationModel {

    public function addBonusConfig($post) {
//        if (($post['mp_1'] + $post['mp_2'] + $post['mp_3'] + $post['per4']) > 100) {
//            return array('status' => -1, 'msg' => '累积奖金分配参数不能超过 100%');
//        }
        $data['name_cn'] = $post['name_cn'] ? $post['name_cn'] : FALSE;
        $data['name_en'] = $post['name_en'] ? $post['name_en'] : FALSE;
        $data['type'] = $post['type'] ? $post['type'] : 1;
        $data['sj'] = $post['sj'] ? $post['sj'] : 1;
        $data['m_1'] = $post['m_1'] ? $post['m_1'] : FALSE;
        $data['m_2'] = $post['m_2'] ? $post['m_2'] : FALSE;
        $data['m_3'] = $post['m_3'] ? $post['m_3'] : FALSE;
        $data['m_4'] = $post['m_4'] ? $post['m_4'] : FALSE;
        $data['mp_1'] = $post['mp_1'] ? $post['mp_1'] : FALSE;
        $data['mp_2'] = $post['mp_2'] ? $post['mp_2'] : FALSE;
        $data['mp_3'] = $post['mp_3'] ? $post['mp_3'] : FALSE;
        $data['mp_4'] = $post['mp_4'] ? $post['mp_4'] : FALSE;
        $data['t_1'] = $post['t_1'] ? $post['t_1'] : FALSE;
        $data['t_2'] = $post['t_2'] ? $post['t_2'] : FALSE;
        $data['t_3'] = $post['t_3'] ? $post['t_3'] : FALSE;
        $data['tp_1'] = $post['tp_1'] ? $post['tp_1'] : FALSE;
        $data['tp_2'] = $post['tp_2'] ? $post['tp_2'] : FALSE;
        $data['tp_3'] = $post['tp_3'] ? $post['tp_3'] : FALSE;
        $data['statu'] = $post['statu'] ? $post['statu'] : 1;
        if ($post['bonus_id'] > 0) {
            $resId = M('bonus')->where(array('bonus_id' => $post['bonus_id']))->save($data);
            if (!$resId) {
                return array('status' => -1, 'msg' => '添加失败');
            } else {
                return array('status' => 1, 'msg' => '添加成功');
            }
        }
    }

    public function addBonusTaxConfig($post) {
        $post['name_cn'] && $data['name_cn'] = $post['name_cn'];
        $post['name_en'] && $data['name_en'] = $post['name_en'];
        $data['money_id'] = $post['money_id'] ? $post['money_id'] : FALSE;
        $data['statu'] = $post['statu'] ? $post['statu'] : 1;
        if ($post['tax_id'] > 0) {
            $resId = M('bonus_tax')->where(array('tax_id' => $post['tax_id']))->save($data);
        } else {
            $resId = M('bonus_tax')->add($data);
        }
        if (!$resId) {
            return array('status' => -1, 'msg' => '添加失败');
        } else {
            return array('status' => 1, 'msg' => '添加成功');
        }
    }

    public function bonusLogClear($post) {
        if ($post['id'] <= 0) {
            return array('status' => -1, 'msg' => '操作失败');
        } else {
            $info = M('bonus_log')->where(array('id' => $post['id']))->find();
            if ($info['statu'] != 9) {
                $bonus = M('bonus')->where(array('id' => $info['bonus_id']))->find();
                bonusSjUnified($bonus, $info['money'], $info['uid'], $info['note'] . $post['name'], 1, $info['come_user_id']);
                if (bonusSjUnified($bonus, $info['money'], $info['uid'], $info['note'], 1, $info['come_user_id'])) {
                    $resId = M('bonus_log')->where(array('id' => $post['id']))->save(array('statu' => 9, 'sj_time' => time(), 'note' => $info['note'] . '，' . $post['name']));
                    $adminId = adminLogAdd($post['name'] . '，手动结算' . $info['note'] . ' 记录ID为： ' . $info['id']);
                    if (!$adminId) {
                        return array('status' => -1, 'msg' => '结算失败');
                    } else {
                        return array('status' => 1, 'msg' => '结算成功');
                    }
                }
            } else {
                return array('status' => -1, 'msg' => '请勿重复结算');
            }
        }
    }

    public function editBonusLogConfig($post) {
        if ($post['id'] <= 0) {
            return array('status' => -1, 'msg' => '操作失败');
        } else {
            $info = M('bonus_log')->where(array('id' => $post['id']))->find();
            if ($info[statu] != 9) {
                $data['money'] = $post['edtiMoney'] ? $post['edtiMoney'] : FALSE;
                $data['note'] = $post['edtiNote'] ? $post['edtiNote'] : FALSE;
                $resId = M('bonus_log')->where(array('id' => $post['id']))->save($data);
                if ($post['edtiMoney'] != $info['money']) {
                    adminLogAdd('修改奖金明细' . $info['note'] . $info['money'] . ' 为 ' . $post['edtiMoney'] . ' ID ' . $info['id']);
                }
                if (!$resId) {
                    return array('status' => -1, 'msg' => '修改失败');
                } else {
                    return array('status' => 1, 'msg' => '修改成功');
                }
            } else {
                return array('status' => -1, 'msg' => '己结算的不支持修改');
            }
        }
    }

}
