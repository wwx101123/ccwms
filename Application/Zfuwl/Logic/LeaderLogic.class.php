<?php

namespace Zfuwl\Logic;

use Think\Model\RelationModel;

class LeaderLogic extends RelationModel {

    protected $tableName = 'leader';

    /**
     * 添加会员
     * @param $user
     * @return array
     */
    public function addLeaderConfig($post) {
        if ($post['name_cn'] == '') {
            return array('status' => -1, 'msg' => '等级名称不能为空');
        }
        $data['name_cn'] = $post['name_cn'] ? $post['name_cn'] : 0;
        $data['name_en'] = $post['name_en'] ? $post['name_en'] : 0;
        $data['per'] = $post['per'] ? $post['per'] : 0;
        $data['mid'] = $post['mid'] > 0 ? $post['mid'] : 0;
        $data['total'] = $post['total'] > 0 ? $post['total'] : 0;
        $data['color'] = $post['color'] ? $post['color'] : 0;
        $data['tjr_num'] = $post['tjr_num'] ? $post['tjr_num'] : 0;
        $data['tjr_num_1'] = $post['tjr_num_1'] ? $post['tjr_num_1'] : 0;
        $data['share_money'] = $post['share_money'] ? $post['share_money'] : 0;
        $data['cun_money'] = $post['cun_money'] ? $post['cun_money'] : 0;
        $data['generalize_per'] = $post['generalize_per'] ? $post['generalize_per'] : 0;

        if ($post['id'] > 0) {
            $agentId = M('leader')->where(array('id' => $post['id']))->save($data);
        } else {
            $agentId = M('leader')->add($data);
        }
        if (!$agentId) {
            return array('status' => -1, 'msg' => '操作失败');
        } else {
            return array('status' => 1, 'msg' => '操作成功');
        }
    }

    public function editUserLeader($post) {
        if ($post['user_id'] <= 0) {
            return array('status' => -1, 'msg' => '刷新后重试');
        }
        if ($post['note'] == '') {
            return array('status' => -1, 'msg' => '备注不能为空');
        }
        $user = M('users')->where(array('user_id' => $post['user_id']))->field('user_id,leader,account')->find();
        if ($post['leader_id'] == $user['leader']) {
            return array('status' => -1, 'msg' => '调整后级别不能与当前相同');
        } else {
            $model = new \Think\Model();
            $model->startTrans();
            $logId = userAction($user['user_id'], $post['note'] . session('admin_name') . '调整领导等级');
            $LeaderId = userLeader($user['user_id'], $user['leader'], $post['leader_id'], 1, $post['note'] . session('admin_name') . '调整');
            $userId = M('users')->where(array('user_id' => $user['user_id']))->save(array('leader' => $post['leader_id']));
            if ($logId && $LeaderId && $userId) {
                $leaderInfo = M('leader')->where("statu=1")->cache('leaderInfo')->getField('id,name_cn');
                adminLogAdd($post['note'] . '调整' . $user['account'] . $leaderInfo[$user[leader]] . '为' . $leaderInfo[$post[leader_id]]);
                $model->commit();
                return array('status' => 1, 'msg' => '操作成功');
            } else {
                $model->rollback();
                return array('status' => -1, 'msg' => '操作失败');
            }
        }
    }

    public function refuseInfo($post) {
        if ($post['id'] > 0) {
            if ($post['name'] == '') {
                return array('status' => -1, 'msg' => '备注不能为空');
            }
            $logInfo = M('leader_log')->where(array('id' => $post['id']))->find();
            if ($logInfo['statu'] == 2) {
                $model = new \Think\Model();
                $model->startTrans();
                $AId = M('leader_log')->where(array('id' => $logInfo['id']))->save(array('statu' => 3, 'refuse' => $post['name'], 'refuse_time' => time(), 'admin_id' => session('admin_id')));
                $user = M('users')->where(array('user_id' => $logInfo['uid']))->field('account')->find();
                $leaderInfo = M('leader')->where("statu=1")->cache('leaderInfo')->getField('id,name_cn');
                $Bid = adminLogAdd('因' . $post['name'] . '拒绝' . $user['account'] . $leaderInfo[$logInfo[x_id]] . '升级申请');
                if ($AId && $Bid) {
                    $model->commit();
                    return array('status' => 1, 'msg' => '操作成功');
                } else {
                    $model->rollback();
                    return array('status' => -1, 'msg' => '操作失败');
                }
            } else {
                return array('status' => -1, 'msg' => '请勿重复操作');
            }
        } else {
            return array('status' => -1, 'msg' => '刷新后重试');
        }
    }

    public function confirmInfo($post) {
        if ($post['id'] > 0) {
            if ($post['name'] == '') {
                return array('status' => -1, 'msg' => '备注不能为空');
            }
            $logInfo = M('leader_log')->where(array('id' => $post['id']))->find();
            if ($logInfo['statu'] == 2) {
                $model = new \Think\Model();
                $model->startTrans();
                $AId = M('leader_log')->where(array('id' => $logInfo['id']))->save(array('statu' => 1, 'confirm' => $post['name'], 'confirm_time' => time(), 'admin_id' => session('admin_id')));
                $user = M('users')->where(array('user_id' => $logInfo['uid']))->field('account,agent')->find();
                $leaderInfo = M('leader')->where("statu=1")->cache('leaderInfo')->getField('id,name_cn');
                $Bid = adminLogAdd($post['name'] . '确认' . $user['account'] . $leaderInfo[$logInfo[x_id]] . '升级申请');
                $Cid = M('users')->where(array('user_id' => $logInfo['uid']))->save(array('leader' => $logInfo[x_id]));
                if ($AId && $Bid && $Cid) {
                    $model->commit();
                    return array('status' => 1, 'msg' => '操作成功');
                } else {
                    $model->rollback();
                    return array('status' => -1, 'msg' => '操作失败');
                }
            } else {
                return array('status' => -1, 'msg' => '请勿重复操作');
            }
        } else {
            return array('status' => -1, 'msg' => '刷新后重试');
        }
    }

}
