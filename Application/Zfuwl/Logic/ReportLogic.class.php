<?php

namespace Zfuwl\Logic;

use Think\Model\RelationModel;

class ReportLogic extends RelationModel {

    protected $tableName = 'bochu_money';

    public function outMoneyAdd($post) {
        if ($post['account'] != '' && $post['mid'] > 0 && $post['money'] > 0 && $post['note'] != '') {
            $user = M('users')->where(array('account' => trim($post['account'])))->field('user_id,nickname')->find();
            if ($user && $post['money'] > 0) {
                $data['zf_time'] = time();
                $data['uid'] = $user['user_id'];
                $data['mid'] = $post['mid'];
                $data['money'] = $post['money'];
                $data['admin_id'] = $_SESSION['admin_id'];
                $data['note'] = $_SESSION['admin_id'];
                $post['note'] && $data['note'] = $post['note'];
                $model = new \Think\Model();
                $model->startTrans();
                $resA = M('bochu_money')->add($data);
                $resB = userMoneyLogAdd($user['user_id'], $post['mid'], $post['money'], 96, $post['note'], $_SESSION['admin_id']);
                if ($resA && $resB) {
                    $model->commit();
                    return array('status' => 1, 'msg' => '操作成功');
                } else {
                    $model->rollback();
                    return array('status' => -1, 'msg' => '操作失败');
                }
            } else {
                return array('status' => -1, 'msg' => $post['account'] . '此会员不存在');
            }
        } else {
            return array('status' => -1, 'msg' => '请核对信息重新提交');
        }
    }

}
