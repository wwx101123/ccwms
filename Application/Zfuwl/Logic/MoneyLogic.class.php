<?php

namespace Zfuwl\Logic;

use Think\Model\RelationModel;

class MoneyLogic extends RelationModel {

    public function addMoneyConfig($post) {
        $post['name_cn'] && $data['name_cn'] = $post['name_cn'];
        $post['name_en'] && $data['name_en'] = $post['name_en'];
        $post['c_pre'] && $data['c_pre'] = $post['c_pre'];
        $post['c_pre'] && $data['c_pre'] = $post['c_pre'];
        $data['t_pre'] = $post['t_pre'] ? $post['t_pre'] : 1;
        $data['is_c'] = $post['is_c'] ? $post['is_c'] : 1;
        $data['is_t'] = $post['is_t'] ? $post['is_t'] : 1;
        $data['statu'] = $post['statu'] ? $post['statu'] : 1;
        if ($post['money_id'] > 0) {
            $resId = M('money')->where(array('money_id' => $post['money_id']))->save($data);
        } else {
            $resId = M('money')->add($data);
            moneyUserAdd($resId);
        }
        if (!$resId) {
            return array('status' => -1, 'msg' => '添加失败');
        } else {
            return array('status' => 1, 'msg' => '添加成功');
        }
    }

    public function moneyLogInfo($post) {
        if ($post['id'] <= 0) {
            return array('status' => -1, 'msg' => '网络失败，请刷新后重试');
        }
        if ($post['name'] == '') {
            return array('status' => -1, 'msg' => '备注不能为空');
        }
        $resId = M('money_log')->where(array('id' => $post['id']))->save(array('note' => $post['name']));
        if (!$resId) {
            return array('status' => -1, 'msg' => '修改失败');
        } else {
            return array('status' => 1, 'msg' => '修改成功');
        }
    }

    public function releaseMoney($data) {
        if ($data['id'] > 0) {
            $list = M('users_money_lock')->where(array('id' => $data['id']))->find();
            if ($list['frozen'] <= 0) {
                return array('status' => -1, 'msg' => '释放金额错误');
            }
            if ($list['statu'] == 2 && $list['frozen'] > 0) {
                $model = new \Think\Model();
                $model->startTrans();
                $userId = M('users_money')->where(array('uid' => $list['uid'], 'mid' => $list['mid']))->setDec('frozen', $list['frozen']);
                $lockId = M('users_money_lock')->where(array('id' => $list['id']))->save(array('statu' => 1, 'out_time' => time(), 'out_note' => $data['name']));
                $user = getUserInfo($list['user_id'], 0);
                adminLogAdd('释放' . $user['account'] . moneyOne($list['mid'])[name_cn] . $list['frozen']);
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

    public function userMoneyEdit($data) {
        if ($data['id'] > 0) {
            if ($data['istype'] <= 0) {
                return array('status' => -1, 'msg' => '请选择操作类型');
            }
            if ($data['editType'] <= 0) {
                return array('status' => -1, 'msg' => '请选择调整类型');
            }
            if ($data['money'] <= 0) {
                return array('status' => -1, 'msg' => '请输入要调整的金额');
            }
            $list = M('users_money')->where(array('id' => $data['id']))->find();
            if ($data['isweb'] == 1) {
                if ($data['webname'] == '') {
                    return array('status' => -1, 'msg' => '请输入公司账号');
                }
                $webuser = M('users')->where(array('account' => trim($data['webname'])))->field('user_id')->find();
                if ($webuser['user_id'] <= 0) {
                    return array('status' => -1, 'msg' => '公司账号不存在');
                }
                $weblist = M('users_money')->where(array('uid' => $webuser['user_id'], 'mid' => $list['mid']))->find();
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
                $note .= '修改' . $user['account'] . moneyOne($list['mid'])[name_cn] . '账户持用余额' . $list['money'] . '为' . ($list['money'] + $data['money']);
                $A = userMoneyLogAdd($list['uid'], $list['mid'], $data['money'], $data['editType'], $data['note'], $_SESSION['admin_id'], $comeUid = '');
                if ($data['isweb'] == 1) {
                    $B = userMoneyLogAdd($webuser['user_id'], $list['mid'], '-' . $data['money'], $data['editType'], $data['note'], $_SESSION['admin_id'], $list['uid']);
                    $note .= '并由' . $data['webname'] . '支出';
                }
            }
            if ($data['istype'] == 2) {
                $note .= '修改' . $user['account'] . moneyOne($list['mid'])[name_cn] . '账户持用余额' . $list['money'] . '为' . ($list['money'] - $data['money']);
                $A = userMoneyLogAdd($list['uid'], $list['mid'], '-' . $data['money'], $data['editType'], $data['note'], $_SESSION['admin_id']);
                if ($data['isweb'] == 1) {
                    $note .= '并回收至' . $data['webname'];
                    $B = userMoneyLogAdd($webuser['user_id'], $list['mid'], $data['money'], $data['editType'], $data['note'], $_SESSION['admin_id'], $list['uid']);
                }
            }
            if ($data['istype'] == 3) {
                $note .= '冻结' . $user['account'] . moneyOne($list['mid'])[name_cn] . $data['money'];
                if (($list['money'] - $list['frozen']) < $data['money']) {
                    $this->error("冻结金额大于当前可用余额" . ($list['money'] - $list['frozen']) . "，请重新输入");
                }
                $A = lockUserMoneyAdd($list['uid'], $data['money'], $list['mid'], 1, $_SESSION['admin_id'] . $data['note']);
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

}
