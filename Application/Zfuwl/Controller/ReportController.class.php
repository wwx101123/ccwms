<?php

namespace Zfuwl\Controller;

class ReportController extends CommonController {

    protected $userModel;

    public function _initialize() {
        parent::_initialize();
        $this->assign('moneyInfo', M('money')->where("statu=1")->cache('moneyInfo')->getField('money_id,name_cn'));
        $this->assign('bonusList', M('bonus')->where("statu=1")->cache('bonusList')->select());
        $this->assign('taxList', M('bonus_tax')->where("statu=1")->cache('taxList')->select());
        $this->assign('levelList', M('level')->where("statu=1")->cache('levelList')->select());
        $this->assign('moneylist', M('money')->where("statu=1")->cache('moneylist')->select());
    }

    public function dayIndex() {
        if (IS_AJAX) {
            $condition = array();
            if (I('time')) {
                $time = explode(' - ', I('time'));
                $addTime = strtotime($time[0]);
                $outTime = strtotime($time[1]) + 86400;
                $condition['zf_time'] = array('between', array($addTime, $outTime));
            }
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('bochu_day')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('bochu_day')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $A = getArrColumn($result, 'uid');
            $B = getArrColumn($result, 'to_uid');
            $userIdArr = array_filter(array_merge($A, $B));
            if ($userIdArr) {
                $this->assign('userList', M('users')->where("user_id in (" . implode(',', $userIdArr) . ")")->getField('user_id,account'));
            }
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('dayIndexAjax');
            die;
        }
        $this->display('dayIndex');
    }

    public function del() {
        $where = array('id' => array('in', I('id')));
        $res = $row = M('bochu_day')->where($where)->delete();
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

    public function emptyBochuDay() {
        $db = M('bochu_day');
        $dbconn = M();
        $tables = array(
            'bochu_day',
        );
        foreach ($tables as $key => $val) {
            $sql = "truncate table " . C("DB_PREFIX") . $val;
            $dbconn->execute($sql);
        }
        $this->success('清除成功!');
    }

    public function outMoneyAdd() {
        if (IS_POST) {
            $data = I('post.');
            $model = new \Zfuwl\Logic\ReportLogic();
            $res = $model->outMoneyAdd($data);
            if ($res['status'] == 1) {
                $this->success('操作成功', U('Report/outMoney'));
                exit;
            } else {
                $this->error('操作失败,' . $res['msg']);
            }
        } else {
            $this->display('outMoneyAdd');
        }
    }

    /**
     * 拨出统计
     */
    public function outMoney() {
        if (IS_AJAX) {
            $condition = array();
            I('account') ? $condition['uid'] = $res = M('users')->where(array('account' => trim(I('account'))))->getField('user_id') : false;
            I('note') && $condition['note'] = array('like', '%' . trim(I('note') . '%'));
            I('mid') ? $condition['mid'] = I('mid') : false;
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            if (I('time')) {
                $time = explode(' - ', I('time'));
                $addTime = strtotime($time[0]);
                $outTime = strtotime($time[1]) + 86400;
                $condition['zf_time'] = array('between', array($addTime, $outTime));
            }
            $count = M('bochu_money')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('bochu_money')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $userIdArr = getArrColumn($result, 'uid');
            if ($userIdArr) {
                $this->assign('userList', M('users')->where("user_id in (" . implode(',', $userIdArr) . ")")->getField('user_id,account'));
            }
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('outMoneyAjax');
            die;
        }
        $this->display('outMoney');
    }

    public function delOutMoney() {
        $where = array('id' => array('in', I('id')));
        $res = $row = M('bochu_money')->where($where)->delete();
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

    public function emptyOutMoney() {
        $db = M('bochu_money');
        $dbconn = M();
        $tables = array(
            'bochu_money',
        );
        foreach ($tables as $key => $val) {
            $sql = "truncate table " . C("DB_PREFIX") . $val;
            $dbconn->execute($sql);
        }
        $this->success('清除成功!');
    }

}
