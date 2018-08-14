<?php

namespace Zfuwl\Logic;

use Think\Model\RelationModel;

class AgentLogic extends RelationModel {

    /**
     * 会员申请记录
     * @param array  $post     会员提交的信息 [note:申请说明,secpwd:二级密码]
     * @param array  $userInfo 会员信息 [user_id:会员id,secpwd:会员的二级密码,agent:服务中心等级]
     * @return array           返回操作信息 [status:-1为失败、1为成功,msg:提示信息]
     */
    public function upAgentAdd($post, $userInfo)
    {

        if(empty($userInfo)) {
            return ['status' => -1, 'msg' => '请先登陆'];
        }

        if($userInfo['agent'] > 0) {
            return ['status' => -1, 'msg' => '已是服务中心'];
        }

        if($post['secpwd'] == '') {
            return ['status' => -1, 'msg' => '请输入二级密码'];
        }
        if(webEncrypt($post['secpwd']) != $userInfo['secpwd']) {
            return ['status' => -1, 'msg' => '二级密码验证失败'];
        }

        $note = htmlspecialchars($post['note']);
        if($note == '') {
            return ['status' => -1, 'msg' => '请输入申请理由'];
        }

        $count = M('agent_log')->where(['uid' => $userInfo['user_id'], 'statu' => 2])->count();
        if($count > 0) {
            return ['status' => -1, 'msg' => '你的申请在确认中'];
        }

        $res = $this->addAgentLog($userInfo['user_id'], $userInfo['agent'], 1, $note);

        if($res) {
            return ['status' => 1, 'msg' => '申请成功'];
        } else {
            return ['status' => -1, 'msg' => '申请失败'];
        }
    }

    /**
     * 添加添加申请记录
     * @param int    $userId 会员id
     * @param int    $yid    原等级id
     * @param int    $xid    现等级id
     * @param string $note   备注说明
     * @param int    $status 状态 1升级成功 2待确认 3失败
     * @return bool
     */
    public function addAgentLog($userId, $yid, $xid, $note, $status = 2)
    {

        $data = [
            'uid' => $userId
            ,'y_id' => $yid
            ,'x_id' => $xid
            ,'zf_time' => time()
            ,'note' => $note
            ,'statu' => $status
        ];

        return M('agent_log')->add($data);
    }

    /**
     * 添加会员
     * @param $user
     * @return array
     */
    public function addAgentConfig($post) {
        if($post['name_cn'] == '') {
            return ['status' => -1, 'msg' => '请输入名称'];
        }
        $post['id'] = intval($post['id']);
        $data = [
            'name_cn' => $post['name_cn']
            ,'b_4' => (float)$post['b_4']
            ,'level_id' => (int)$post['level_id']
        ];
        if ($post['id'] > 0) {
            $agentId = M('agent')->where(['id' => $post['id']])->save($data);
        } else {
            $agentId = M('agent')->add($data);
        }
        if (!$agentId) {
            return ['status' => -1, 'msg' => '操作失败'];
        } else {
            return ['status' => 1, 'msg' => '操作成功', 'agentId' => $agentId];
        }
    }

    public function editUserAgent($post) {
        if ($post['user_id'] <= 0) {
            return array('status' => -1, 'msg' => '刷新后重试');
        }
        if ($post['note'] == '') {
            return array('status' => -1, 'msg' => '备注不能为空');
        }
        $user = M('users')->where(array('user_id' => $post['user_id']))->field('user_id,agent,account')->find();
        if ($post['agent_id'] == $user['agent']) {
            return array('status' => -1, 'msg' => '调整后级别不能与当前相同');
        } else {
            $model = new \Think\Model();
            $model->startTrans();
            $logId = userAction($user['user_id'], $post['note'] . session('admin_name') . '调整代理等级');
            $AgentId = userAgent($user['user_id'], $user['agent'], $post['agent_id'], 1, $post['note'] . session('admin_name') . '调整');
            $userId = M('users')->where(array('user_id' => $user['user_id']))->save(array('agent' => $post['agent_id']));
            if ($logId && $AgentId && $userId) {
                $agentInfo = M('agent')->where("statu=1")->cache('agentInfo')->getField('id,name_cn');
                adminLogAdd($post['note'] . '调整' . $user['account'] . $agentInfo[$user[agent]] . '为' . $agentInfo[$post[agent_id]]);
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
            $logInfo = M('agent_log')->where(array('id' => $post['id']))->find();
            if ($logInfo['statu'] == 2) {
                $model = new \Think\Model();
                $model->startTrans();
                $AId = M('agent_log')->where(array('id' => $logInfo['id']))->save(array('statu' => 3, 'refuse' => $post['name'], 'refuse_time' => time(), 'admin_id' => session('admin_id')));
                $user = M('users')->where(array('user_id' => $logInfo['uid']))->field('account')->find();
                $agentInfo = M('agent')->where("statu=1")->cache('agentInfo')->getField('id,name_cn');
                $Bid = adminLogAdd('因' . $post['name'] . '拒绝' . $user['account'] . $agentInfo[$logInfo[x_id]] . '升级申请');
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
            $logInfo = M('agent_log')->where(array('id' => $post['id']))->find();
            if ($logInfo['statu'] == 2) {
                $model = new \Think\Model();
                $model->startTrans();
                $AId = M('agent_log')->where(array('id' => $logInfo['id']))->save(array('statu' => 1, 'confirm' => $post['name'], 'confirm_time' => time(), 'admin_id' => session('admin_id')));
                $user = M('users')->where(array('user_id' => $logInfo['uid']))->field('account,agent')->find();
                $agentInfo = M('agent')->where("statu=1")->cache('agentInfo')->getField('id,name_cn');
                $Bid = adminLogAdd($post['name'] . '确认' . $user['account'] . $agentInfo[$logInfo['x_id']] . '升级申请');
                $Cid = M('users')->where(array('user_id' => $logInfo['uid']))->save(array('agent' => $logInfo['x_id']));
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
