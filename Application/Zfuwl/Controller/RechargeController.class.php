<?php

/**
 *  众福网络直销系统管理软件
 * ============================================================================
 * 版权所有 2015-2027 深圳市众福网络软件有限公司，并保留所有权利。
 * 网站地址: http://www.zfuwl.com   http://www.jiafuw.com
 * 联系方式：qq:1845218096 电话：15899929162
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Author:  众福团队
 * Date:2016-12-10 21:30  154
 */

namespace Zfuwl\Controller;

class RechargeController extends CommonController {

    public function _initialize() {
        parent::_initialize();
        $this->assign('payStatu', payStatu());
        $this->assign('addMoneyType', addMoneyType());
        $this->assign('moneyInfo', M('money')->where("statu=1")->cache('moneyInfo')->getField('money_id,name_cn'));
        $this->assign('bankInfo', M('bank')->where("statu=1")->cache('bankInfo')->getField('id,name_cn'));
    }

    public function index() {
        if (IS_AJAX) {
            $condition = array();
            I('mid') ? $condition['mid'] = I('mid') : false;
            I('statu') ? $condition['statu'] = I('statu') : false;
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('pay_config')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('pay_config')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('indexAjax');
            die;
        }
        $this->display('index');
    }

    public function add() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\RechargeLogic();
            $res = $model->payInfo($data);
            if ($res['status'] != 1) {
                $this->error($res['msg']);
            } else {
                $this->success($res['msg'], U('Recharge/index'));
            }
        } else {
            $this->display('payInfo');
        }
    }

    public function edit() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\RechargeLogic();
            $res = $model->payInfo($data);
            if ($res['status'] != 1) {
                $this->error($res['msg']);
            } else {
                $this->success($res['msg'], U('Recharge/index'));
            }
        } else {
            $this->assign('info', M('pay_config')->where(array('id' => I('id')))->find());
            $this->display('payInfo');
        }
    }

    public function saveStatu() {
        if (IS_POST) {
            $res = M('pay_config')->where(array('id' => I('id')))->save(array('statu' => I('val')));
            if ($res) {
                $this->success('更新成功!');
            } else {
                $this->error('更新失败!');
            }
        }
    }

    public function del() {
        $where = array('id' => array('in', I('id')));
        $res = $row = M('pay_config')->where($where)->delete();
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

    public function payLog() {
        if (IS_AJAX) {
            $condition = array();
            I('account') ? $condition['uid'] = $res = M('users')->where(array('account' => trim(I('account'))))->getField('user_id') : false;
            I('note') && $condition['note'] = array('like', '%' . trim(I('note') . '%'));
            I('order_sn') && $condition['order_sn'] = array('like', '%' . trim(I('order_sn') . '%'));
            I('mid') ? $condition['mid'] = I('mid') : false;
            $add_time = strtotime(urldecode(trim(I('start_time'))));
            $out_time = strtotime(urldecode(trim(I('end_time'))));
            if (I('time')) {
                $time = explode(' - ', I('time'));
                $addTime = strtotime($time[0]);
                $outTime = strtotime($time[1]) + 86400;
                $condition['add_time'] = array('between', array($addTime, $outTime));
            }
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('pay_recharge')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('pay_recharge')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $userIdArr = getArrColumn($result, 'uid');
            if ($userIdArr) {
                $this->assign('userList', M('users')->where("user_id in (" . implode(',', $userIdArr) . ")")->getField('user_id,account'));
            }
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('payLogAjax');
            die;
        }
        $this->display('payLog');
    }

    public function delPayLog() {
        $where = array('id' => array('in', I('id')));
        $res = $row = M('pay_recharge')->where($where)->delete();
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

    public function emptyPayLog() {
        $db = M('pay_recharge');
        $dbconn = M();
        $tables = array(
            'pay_recharge',
        );
        foreach ($tables as $key => $val) {
            $sql = "truncate table " . C("DB_PREFIX") . $val;
            $dbconn->execute($sql);
        }
        $this->success('清除成功!');
    }

    public function addUserMoney() {
        if (IS_AJAX) {
            $condition = array();
            I('account') ? $condition['uid'] = $res = M('users')->where(array('account' => trim(I('account'))))->getField('user_id') : false;
            I('note') && $condition['note'] = array('like', '%' . trim(I('note') . '%'));
            I('bank_id') ? $condition['bank_id'] = I('bank_id') : false;
            I('mid') ? $condition['mid'] = I('mid') : false;
            I('type') ? $condition['type'] = I('type') : false;
            if (I('time')) {
                $time = explode(' - ', I('time'));
                $addTime = strtotime($time[0]);
                $outTime = strtotime($time[1]) + 86400;
                $condition['add_time'] = array('between', array($addTime, $outTime));
            }
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('users_money_add')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('users_money_add')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $userIdArr = getArrColumn($result, 'uid');
            if ($userIdArr) {
                $this->assign('userList', M('users')->where("user_id in (" . implode(',', $userIdArr) . ")")->getField('user_id,account'));
            }
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('addUserMoneyAjax');
            die;
        }
        $this->display('addUserMoney');
    }

    public function affirmUserMoneyAdd() {
        $info = M('users_money_add')->where(array('id' => $_POST['id']))->find();
        if (!$info || $info['type'] == 1) {
            $this->error('网络失败，请刷新页面后重试');
        }
        if ($info['type'] == 2) {
            $model = new \Think\Model();
            $model->startTrans();
            $res = M('users_money_add')->where(array('id' => $info['id']))->save(array('type' => 1, 'affirm_time' => time(), 'admin_id' => session('admin_id')));
            $info = userMoneyLogAdd($info['uid'], $info['mid'], $info['actual_money'], 103, $info['note'], session('admin_id'));
            if ($res && $info) {
                $model->commit();
                $this->success('确认成功');
            } else {
                $model->rollback();
                $this->error('操作失败，请刷新页面后重试');
            }
        }
    }

    public function refuseUserMoneyAdd() {
        if ($_POST) {
            $res = M('users_money_add')->where(array('id' => $_POST['id']))->save(array('type' => 3, 'refuse' => $_POST['name'], 'refuse_time' => time(), 'admin_id' => session('admin_id')));
            if ($res) {
                $this->success('操作成功');
            } else {
                $this->error('操作失败，请刷新页面后重试');
            }
        }
    }

    public function delAddUserMoneyLog() {
        $where = array('id' => array('in', I('id')));
        $res = $row = M('users_money_add')->where($where)->delete();
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

    public function emptyAddUserMoneyLog() {
        $db = M('users_money_add');
        $dbconn = M();
        $tables = array(
            'users_money_add',
        );
        foreach ($tables as $key => $val) {
            $sql = "truncate table " . C("DB_PREFIX") . $val;
            $dbconn->execute($sql);
        }
        $this->success('清除成功!');
    }

}
