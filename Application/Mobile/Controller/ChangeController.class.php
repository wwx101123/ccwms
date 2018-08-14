<?php

namespace Mobile\Controller;

use Think\Crypt\Driver\Think;
use Zfuwl\Logic\ChangeLogic;


class ChangeController extends CommonController {

    protected $changeLogic;

    public function _initialize() {
        parent::_initialize();
        $this->changeLogic = new ChangeLogic();
    }

    /**
     * 会员转账记录
     */
    public function changeIndex() {
        $condition = array();
        $condition['uid'] = $this->user_id;
        $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
        $count = M('money_change_log')->where($condition)->count();
        $count2 = M('money_change_log')->where(array('to_uid' => $this->user_id))->count();
        if (IS_AJAX) {
            $p = I('p') > 0 ? I('p') : 0;
            $pSize = 10;
            $result = M('money_change_log')->where($condition)->order($sort_order)->limit($p * $pSize, $pSize)->select();
            $toUserIdArr = getArrColumn($result, 'to_uid');
            if ($toUserIdArr) {
                $userList = M('users')->where("user_id in(" . implode(',', $toUserIdArr) . ")")->getField('user_id, account');
                $this->assign('userList', $userList);
            }
            $this->assign('list', $result);
            $this->display('changeIndexAjax');
        } else {
            $this->assign('count', $count);
            $this->assign('count2', $count2);
            $this->display('changeIndex');
        }
    }

    /**
     * 会员转入记录
     */
    public function changeIndex2() {
        $condition = array();
        $condition['to_uid'] = $this->user_id;
        $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
        if (IS_AJAX) {
            $p = I('p') > 0 ? I('p') : 0;
            $pSize = 10;
            $result = M('money_change_log')->where($condition)->order($sort_order)->limit($p * $pSize, $pSize)->select();
            $toUserIdArr = getArrColumn($result, 'uid');
            if ($toUserIdArr) {
                $userList = M('users')->where("user_id in(" . implode(',', $toUserIdArr) . ")")->getField('user_id, account');
                $this->assign('userList', $userList);
            }
            $this->display('changeIndexAjax2');
        }
    }

    /**
     * 会员钱包转账
     */
    public function changeAdd() {
        if (IS_POST) {
            $data = I('post.');
            $res = $this->changeLogic->changeAdd($data, $this->user_id);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Change/changeIndex'));
                exit;
            } else {
                $this->error($res['msg']);
            }
        } else {
            $id = I("id", '', 'intval');
            $changeInfo = M('money_change')->where(array('id' => $id))->find();
            $this->assign('changeInfo', $changeInfo);

            $this->display('changeAdd');
        }
    }

    /**
     * 领取积分
     */
    public function getIntegral()
    {
        if (IS_POST) {
          	
          	$post = I('post.');

            if ($post['lockMoney'] <= 0) {
                $this->error('暂无积分领取');
            }

            if ($post['money'] <= 0) {
                $this->error('暂无积分领取');
            }
          
          	if (zfCache('securityInfo.is_systec_test') == 1) {
                $list = M('block_release_money')->where(['uid' => $this->user_id, 'status' => 1])->find();
                if ($list['last_time'] >= strtotime(date('Y-m-d'))) {
                    $this->error('每日限领一次');
                }
            }
            $res = getIntegral($this->user_id);
            if ($res) {
                $this->success('操作成功', U('Change/getIntegral'));
            } else {
                $this->error('操作失败');
            }
            exit;
        }
        $this->assign('money', M('block_release_money')->where(['uid' => $this->user_id, 'status' => 1])->sum('fh_money'));
      	$this->assign('lockMoney', M('block_release_money')->where(['uid' => $this->user_id, 'status' => 1])->sum('stay_money'));
        $this->display('getIntegral');
    }

    /**
     * 储存YML转出
     */
        /**
     * 储存YML转出
     */
    public function storeRollout() {
        if (IS_POST) {
            $user = $this->user;
            $data = I('post.');
            if (empty($data['num'])) {
                $this->error('请输入转出个数');
            }
            if (empty($data['secpwd'])) {
                $this->error('请输入交易密码');
            }

            if (webEncrypt($data['secpwd']) != $user['secpwd']) {
                $this->error('交易密码错误');
            }

            $res = smsCodeVerify($user['account'], $data['mobileCode'], session_id());
            if ($res['status'] != 1) {
                $this->ajaxReturn(array('status' => 0, 'msg' => $res['msg']));
            }

            $model = new \Think\Model();
            $model->startTrans();

            # 减去 总投资金额
            $num = M('block_log')->where(['uid' => $this->user_id, 'is_type' => 110])->count();
            $price = M('block_log')->where(['uid' => $this->user_id, 'is_type' => 110])->sum('per');
            $averagePrice = round(($price / $num), 2);
            $totalPrice = $data['num'] * $averagePrice;
            M('users')->where(['user_id' => $this->user_id])->setDec('invest_money', $totalPrice);
			userAction($this->user_id, '总投资金额 - ' . $totalPrice);
          
            # 减去 存储数量
            $A = M('block_user')->where(['uid' => $this->user_id])->setDec('deposit', $data['num']);
            $B = userAction($this->user_id, '转出存储' . blockList(1, 1));
            $info = [];
            $info['uid'] = $this->user_id;
            $info['add_time'] = time();
            $info['money'] = blockList(1, 2) * $data['num'];
            if ($info['money'] > zfCache('securityInfo.shi_money')) {
                $info['per'] = zfCache('securityInfo.shi_perb');
            } else {
                $info['per'] = zfCache('securityInfo.shi_pera');
            }
            $info['price'] = blockList(1, 2);
            $info['num'] = $data['num'];
            $info['fh_money'] = round(($info['money'] * $info['per']), 2) / 100;
          	$info['stay_money'] = $info['money'];
            $info['fh_num'] = 0;
            $info['fh_total'] = 0;
            $info['status'] = 1;
            $res = M('block_release_money')->add($info);
            if ($A && $B && $res) {
                $model->commit();
                $this->success('操作成功', U('Change/storeRollout'));
            } else {
                $model->rollback();
                $this->error('操作失败');
            }
            exit;
        }
        $this->assign('info', M('block_user')->where(['uid' => $this->user_id, 'bid' => 1])->find());
        $this->display('storeRollout');
    }

    /**
     * 积分转出
     */
    public function pointsRoll() {
        $money = usersMoney($this->user_id, zfCache('securityInfo.zc_mid'), 1);

        $block = $money / blockList(1, 2);
        if (IS_POST) {
            $user = $this->user;
            $data = I('post.');

            if (empty($data['num'])) {
                $this->error('请输入领取个数');
            }

            if (empty($data['secpwd'])) {
                $this->error('请输入交易密码');
            }

            $blockNum = M('block_log')->where(['uid' => $this->user_id, 'is_type' => 10])->sum('money');

            if ($data['num'] >= $block) {
                $this->error('你最多可以领取' . $block);
            }

            $shu = $block - $blockNum;

            if (($data['num'] + $blockNum) >= $block) {
                $this->error('你最多还可以领取' . $shu);
            }

            if (webEncrypt($data['secpwd']) != $user['secpwd']) {
                $this->error('交易密码错误');
            }

            $res = smsCodeVerify($user['account'], $data['mobileCode'], session_id());
            if ($res['status'] != 1) {
                $this->ajaxReturn(array('status' => 0, 'msg' => $res['msg']));
            }

            $model = new \Think\Model();
            $model->startTrans();

            $A = userBlockLogAdd($this->user_id, 1, $data['num'], 10, '积分转出');
            $B = userMoneyLogAdd($this->user_id, zfCache('securityInfo.sh_mid'), '-' . (blockList(1, 2) * $data['num']), 9, '积分转出');

            if ($A && $B) {
                $model->commit();
                $this->success('操作成功', U('Change/pointsRoll'));
            } else {
                $model->rollback();
                $this->error('操作失败');
            }
            exit;
        }

        $this->assign('block', intval($block));
        $this->display('pointsRoll');
    }
}
