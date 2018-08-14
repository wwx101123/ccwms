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

class MoneyController extends CommonController {

    public function _initialize() {
        $this->assign('moneyInfo', M('money')->where("statu=1")->cache('moneyInfo')->getField('money_id,name_cn'));
        $this->assign('bonusList', M('bonus')->where("statu=1")->cache('bonusList')->select());
        $this->assign('taxList', M('bonus_tax')->where("statu=1")->cache('taxList')->select());
        $this->assign('moneylist', M('money')->where("statu=1")->cache('moneylist')->select());
        $this->assign('moneyLogType', moneyLogType());
        $this->assign('lockStatu', lockStatu());
        parent::_initialize();
    }

    public function index() {
        if (IS_AJAX) {
            $condition = array();
            I('name_cn') && $condition['name_cn'] = array('like', '%' . trim(I('name_cn') . '%'));
            I('statu') ? $condition['statu'] = I('statu') : false;
            I('is_t') ? $condition['is_t'] = I('is_t') : false;
            I('is_c') ? $condition['is_c'] = I('is_c') : false;
            $sort_order = I('order_by', 'money_id') . ' ' . I('sort', 'desc');
            $count = M('money')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('money')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('indexAjax');
            die;
        }
        $this->display('index');
    }

    public function addMoney() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\MoneyLogic();
            $res = $model->addMoneyConfig($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Money/index'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->display('moneyInfo');
        }
    }

    public function editMoney() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\MoneyLogic();
            $res = $model->addMoneyConfig($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Money/index'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->assign('info', M('money')->where(array('money_id' => I('id')))->find());
            $this->display('moneyInfo');
        }
    }

    public function saveStatu() {
        if (IS_POST) {
            $res = M('money')->where(array('money_id' => I('id')))->save(array('statu' => I('val')));
            if ($res) {
                $this->success('更新成功!');
            } else {
                $this->error('更新失败!');
            }
        }
    }

    public function saveIsc() {
        if (IS_POST) {
            $res = M('money')->where(array('money_id' => I('id')))->save(array('is_c' => I('val')));
            if ($res) {
                $this->success('更新成功!');
            } else {
                $this->error('更新失败!');
            }
        }
    }

    public function saveIst() {
        if (IS_POST) {
            $res = M('money')->where(array('money_id' => I('id')))->save(array('is_t' => I('val')));
            if ($res) {
                $this->success('更新成功!');
            } else {
                $this->error('更新失败!');
            }
        }
    }

    public function delMoney() {
        $monenNum = M('users_money')->where(array('money_id' => I('id')))->sum('money');
        if ($monenNum > 0) {
            $this->error('当前钱包总余额' . $monenNum . '请勿删除');
        }
        $where = array('money_id' => array('in', I('id')));
        $res = $row = M('money')->where($where)->delete();
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

    public function moneyLog() {
        if (IS_AJAX) {
            $condition = array();
            I('account') ? $condition['uid'] = $res = M('users')->where(array('account' => trim(I('account'))))->getField('user_id') : false;
            I('to_account') ? $condition['come_uid'] = $res = M('users')->where(array('account' => trim(I('to_account'))))->getField('user_id') : false;
            I('note') && $condition['note'] = array('like', '%' . trim(I('note') . '%'));
            I('money_id') ? $condition['mid'] = I('money_id') : false;
            I('is_type') ? $condition['is_type'] = I('is_type') : false;
            if (I('time')) {
                $time = explode(' - ', I('time'));
                $addTime = strtotime($time[0]);
                $outTime = strtotime($time[1]) + 86400;
                $condition['zf_time'] = array('between', array($addTime, $outTime));
            }
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('money_log')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('money_log')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $A = getArrColumn($result, 'uid');
            $B = getArrColumn($result, 'come_uid');
            $userIdArr = array_filter(array_merge($A, $B));
            if ($userIdArr) {
                $this->assign('userList', M('users')->where("user_id in (" . implode(',', $userIdArr) . ")")->getField('user_id,account'));
            }
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('moneyLogAjax');
            die;
        }
        $this->display('moneyLog');
    }

    public function editMoneyLog() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\MoneyLogic();
            $res = $model->moneyLogInfo($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Money/moneyLog'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->display('moneyLogInfo');
        }
    }

    public function delMoneyLog() {
        $where = array('id' => array('in', I('id')));
        $res = M('money_log')->where($where)->delete();
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

    public function emptyMoneyLog() {
        $db = M('money_log');
        $dbconn = M();
        $tables = array(
            'money_log',
        );
        foreach ($tables as $key => $val) {
            $sql = "truncate table " . C("DB_PREFIX") . $val;
            $dbconn->execute($sql);
        }
        $this->success('清除成功!');
    }

    public function userDay() {
        if (IS_AJAX) {
            $condition = array();
            I('account') ? $condition['uid'] = $res = M('users')->where(array('account' => trim(I('account'))))->getField('user_id') : false;
            if (I('time')) {
                $time = explode(' - ', I('time'));
                $addTime = strtotime($time[0]);
                $outTime = strtotime($time[1]) + 86400;
                $condition['zf_time'] = array('between', array($addTime, $outTime));
            }
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('users_day')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('users_day')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $userIdArr = getArrColumn($result, 'uid');
            if ($userIdArr) {
                $this->assign('userList', M('users')->where("user_id in (" . implode(',', $userIdArr) . ")")->getField('user_id,account'));
            }
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('userDayAjax');
            die;
        }
        $this->display('userDay');
    }

    public function editUserDay() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\MoneyLogic();
            $res = $model->moneyLogInfo($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Money/moneyLog'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->display('moneyLogInfo');
        }
    }

    public function delUserDay() {
        $where = array('id' => array('in', I('id')));
        $res = M('users_day')->where($where)->delete();
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

    public function emptyUserDay() {
        $db = M('users_day');
        $dbconn = M();
        $tables = array(
            'users_day',
        );
        foreach ($tables as $key => $val) {
            $sql = "truncate table " . C("DB_PREFIX") . $val;
            $dbconn->execute($sql);
        }
        $this->success('清除成功!');
    }

    public function userMoney() {
        if (IS_AJAX) {
            $condition = array();
            I('account') ? $condition['uid'] = $res = M('users')->where(array('account' => trim(I('account'))))->getField('user_id') : false;
            I('mid') ? $condition['mid'] = I('mid') : false;
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('users_money')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('users_money')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $userIdArr = getArrColumn($result, 'uid');
            if ($userIdArr) {
                $this->assign('userList', M('users')->where("user_id in (" . implode(',', $userIdArr) . ")")->getField('user_id,account'));
            }
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('userMoneyAjax');
            die;
        }
        $this->display('userMoney');
    }

    public function lockUserMoney() {
        if (IS_AJAX) {
            $condition = array();
            I('account') ? $condition['uid'] = $res = M('users')->where(array('account' => trim(I('account'))))->getField('user_id') : false;
            I('mid') ? $condition['mid'] = I('mid') : false;
            I('statu') ? $condition['statu'] = I('statu') : false;
            if (I('time')) {
                $time = explode(' - ', I('time'));
                $addTime = strtotime($time[0]);
                $outTime = strtotime($time[1]) + 86400;
                $condition['lock_time'] = array('between', array($addTime, $outTime));
            }
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('users_money_lock')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('users_money_lock')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $userIdArr = getArrColumn($result, 'uid');
            if ($userIdArr) {
                $this->assign('userList', M('users')->where("user_id in (" . implode(',', $userIdArr) . ")")->getField('user_id,account'));
            }
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('lockUserMoneyAjax');
            die;
        }
        $this->display('lockUserMoney');
    }

    public function releaseMoney() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\MoneyLogic();
            $res = $model->releaseMoney($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Money/lockUserMoney'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->display('lockUserMoney');
        }
    }

    public function userMoneyEdit() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\MoneyLogic();
            $res = $model->userMoneyEdit($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Money/userMoney'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $info = M('users_money')->where(array('id' => I('id')))->find();
            $this->assign('info', $info);
            $this->assign('user', M('users')->where(array('user_id' => $info['uid']))->field('account')->find());
            $this->display('userMoneyEdit');
        }
    }

}
