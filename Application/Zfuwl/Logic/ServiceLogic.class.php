<?php

namespace Zfuwl\Logic;

use Think\Model\RelationModel;

class ServiceLogic extends RelationModel {

    protected $tableName = 'service';

    /**
     * 添加会员
     * @param $user
     * @return array
     */
    public function addServiceConfig($post) {
        if ($post['name_cn'] == '') {
            return array('status' => -1, 'msg' => '等级名称不能为空');
        }
        if ($post['per'] == '') {
            return array('status' => -1, 'msg' => '奖金比例不能为空');
        }
        if ($post['amount'] == '') {
            return array('status' => -1, 'msg' => '申请金额不能为空');
        }
        $data['name_cn'] = $post['name_cn'] ? $post['name_cn'] : 0;
        $data['name_en'] = $post['name_en'] ? $post['name_en'] : 0;
        $data['per'] = $post['per'] ? $post['per'] : 0;
        $data['amount'] = $post['amount'] ? $post['amount'] : 0;
        $data['team_money'] = $post['team_money'] > 0 ? $post['team_money'] : 0;
        $data['color'] = $post['color'] ? $post['color'] : 0;
        $data['statu'] = $post['statu'] ? $post['statu'] : 2;
        $data['xz_sq'] = $post['xz_sq'] ? $post['xz_sq'] : 2;
        $data['give_shares'] = $post['give_shares'] ? $post['give_shares'] : 0;
        $data['zt_num'] = $post['zt_num'] ? $post['zt_num'] : 0;
        $data['team_num'] = $post['team_num'] ? $post['team_num'] : 0;
        if ($post['id'] > 0) {
            $agentId = M('service')->where(array('id' => $post['id']))->save($data);
        } else {
            $agentId = M('service')->add($data);
        }
        if (!$agentId) {
            return array('status' => -1, 'msg' => '操作失败');
        } else {
            return array('status' => 1, 'msg' => '操作成功', 'agentId' => $agentId);
        }
    }

    public function editUserService($post) {
        if ($post['user_id'] <= 0) {
            return array('status' => -1, 'msg' => '刷新后重试');
        }
        if ($post['note'] == '') {
            return array('status' => -1, 'msg' => '备注不能为空');
        }
        $post['province'] = intval($post['province']);
        $post['city'] = intval($post['city']);
        $post['district'] = intval($post['district']);
        if ($post['province'] <= 0 || $post['city'] <= 0 || $post['district'] <= 0) {
            return array('status' => -1, 'msg' => '请选择代理地区');
        }
        $user = M('users')->where(array('user_id' => $post['user_id']))->field('user_id,tjr_id,service,account')->find();
        if ($post['service_id'] == $user['service']) {
            return array('status' => -1, 'msg' => '调整后级别不能与当前相同');
        } else {
            $model = new \Think\Model();
            $model->startTrans();
            $logId = userAction($user['user_id'], $post['note'] . session('admin_name') . '调整会员等级');
            $ServiceId = userService($user['user_id'], $user['service'], $post['service_id'], 1, $post['note'] . session('admin_name') . '调整', $post['province'], $post['city'], $post['district']);
            $userId = M('users')->where(array('user_id' => $user['user_id']))->save(array('service' => $post['service_id']));
            if ($logId && $ServiceId && $userId) {
                $data = array(
                    'service' => $post['service_id'],
                    'dl_province' => $post['province'],
                    'dl_city' => $post['city'],
                    'dl_district' => $post['district']
                );
                $res = (new UserLogic())->saveUserInfo($data, $user['user_id']);
                $serviceInfo = M('service')->where("statu=1")->cache('serviceInfo')->getField('id,name_cn');
                adminLogAdd($post['note'] . '调整' . $user['account'] . $serviceInfo[$user[service]] . '为' . $serviceInfo[$post[service_id]]);
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
            $logInfo = M('service_log')->where(array('id' => $post['id']))->find();
            if ($logInfo['statu'] == 2) {
                $model = new \Think\Model();
                $model->startTrans();
                $AId = M('service_log')->where(array('id' => $logInfo['id']))->save(array('statu' => 3, 'refuse' => $post['name'], 'refuse_time' => time(), 'admin_id' => session('admin_id')));
                $user = M('users')->where(array('user_id' => $logInfo['uid']))->field('account')->find();
                $serviceInfo = M('service')->where("statu=1")->cache('serviceInfo')->getField('id,name_cn');
                $Bid = adminLogAdd('因' . $post['name'] . '拒绝' . $user['account'] . $serviceInfo[$logInfo[x_id]] . '升级申请');
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
            $logInfo = M('service_log')->where(array('id' => $post['id']))->find();
            if ($logInfo['statu'] == 2) {
                $model = new \Think\Model();
                $model->startTrans();
                $AId = M('service_log')->where(array('id' => $logInfo['id']))->save(array('statu' => 1, 'confirm' => $post['name'], 'confirm_time' => time(), 'admin_id' => session('admin_id')));
                $user = M('users')->where(array('user_id' => $logInfo['uid']))->field('account,agent')->find();
                $serviceInfo = M('service')->where("statu=1")->cache('serviceInfo')->getField('id,name_cn');
                $Bid = adminLogAdd($post['name'] . '确认' . $user['account'] . $serviceInfo[$logInfo[x_id]] . '升级申请');
                $Cid = M('users')->where(array('user_id' => $logInfo['uid']))->save(array('service' => $logInfo[x_id]));
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

    /**
     * 会员申请代理
     * @param array $post 会员提交的数据
     * @param int $uid 会员id
     * @return array 操作状态
     */
    public function upServiceAdd($post, $uid) {
        $user = M('users')->where(array('user_id' => $uid))->field('user_id, service, secpwd, tjr_id, account')->find();
        if (!$user) {
            return array('status' => -1, 'msg' => '请先登录后再试');
        }
        if ($post['secpwd'] == '') {
            return array('status' => -1, 'msg' => '请输入二级密码');
        }
        if (webEncrypt($post['secpwd']) != $user['secpwd']) {
            return array('status' => -1, 'msg' => '二级密码验证失败');
        }
        $post['service_id'] = intval($post['service_id']);
        if ($post['service_id'] <= 0) {
            return array('status' => -1, 'msg' => '请选择代理等级');
        }
        if ($post['service_id'] == $user['service']) {
            return array('status' => -1, 'msg' => '申请等级不能与当前等级一样');
        }
        $post['province'] = intval($post['province']);
        $post['city'] = intval($post['city']);
        $post['district'] = intval($post['district']);
        if ($post['province'] <= 0 || $post['city'] <= 0 || $post['district'] <= 0) {
            return array('status' => -1, 'msg' => '请选择代理地区');
        }
        $service = M('service')->where("id = {$post['service_id']}")->find();
        if ($post['payment_code'] != 'alipay') {
            if (usersMoney($uid, $post['payment_code'], 1) < $service['amount']) {
                return array('status' => -1, 'msg' => '余额不足!');
            }
        }

        // switch ($service['id']) {
        //     case 1:
        //         $districtCount = M('users_data')->where(array('dl_district' => $post['district']))->count();
        //         if ($districtCount >= $service['xz_sq']) {
        //             return array('status' => -1, 'msg' => '该区域代理已存在');
        //         }
        //         break;
        //     case 2:
        //         $cityCount = M('users_data')->where(array('dl_city' => $post['city']))->count();
        //         if ($cityCount >= $service['xz_sq']) {
        //             return array('status' => -1, 'msg' => '该市代理已存在');
        //         }
        //     case 2:
        //         $provinceCount = M('users_data')->where(array('dl_province' => $post['city']))->count();
        //         if ($provinceCount >= $service['xz_sq']) {
        //             return array('status' => -1, 'msg' => '该省代理已存在');
        //         }
        //         break;
        // }

        $res = userMoneyLogAdd($uid, $post['payment_code'], '-' . $service['amount'], 118, '申请' . $service['name_cn']);
        if (!$res) {
            return array('status' => -1, 'msg' => '操作失败');
        }

        $data = array(
            'service' => $service['id'],
            'dl_province' => $post['province'],
            'dl_city' => $post['city'],
            'dl_district' => $post['district']
        );
        $res = (new UserLogic())->saveUserInfo($data, $uid);
        // $res = M('users')->where(array('user_id' => $uid))->save($data);
        if ($res['status'] == 1) {
            $data = array(
                'uid' => $uid,
                'y_id' => $user['service'],
                'x_id' => $service['id'],
                'zf_time' => time(),
                'note' => '申请',
                'statu' => 1,
                'province' => $post['province'],
                'city' => $post['city'],
                'district' => $post['district']
            );
            M('service_log')->add($data);
            bonus1ClearSeller($user['tjr_id'], $user['user_id'], $service['amount'], $user['account'] . '成为'.$service['name_cn']);
            giveUserShares($user['user_id'],$service['give_shares'],'开通'.$service['name_cn'].'赠送');
            autoUpgradeService($user['tjr_id']);
            // bonus13Clear($user['tjr_id'], $user['user_id'], $service['id'], $user['account'] . '成为代理');
//            serviceClearForXj($user['user_id'], $user['account'].'成为代理');
            // if ($service['givea_m'] > 0 && $service['givea_p'] > 0) {
            //     $giveaId = userMoneyLogAdd($uid, $service['givea_m'], $service['givea_p'], 107, '申请代理赠送');
            // }
            // if ($service['giveb_m'] > 0 && $service['giveb_p'] > 0) {
            //     $givebId = userMoneyLogAdd($uid, $service['giveb_m'], $service['giveb_p'], 107, '申请代理赠送');
            // }
            return array('status' => 1, 'msg' => '升级成功', 'url' => U('User/userIndex'));
        } else {
            return array('status' => -1, 'msg' => '升级失败');
        }
    }

}
