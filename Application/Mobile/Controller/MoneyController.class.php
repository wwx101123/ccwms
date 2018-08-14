<?php

namespace Mobile\Controller;

class MoneyController extends CommonController {

    public function _initialize() {
        parent::_initialize();
        $this->assign('moneyInfo', M('money')->where("statu=1")->cache('moneyInfo')->getField('money_id,name_cn'));
        $this->assign('bonusList', M('bonus')->where("statu=1")->cache('bonusList')->select());
        $this->assign('taxList', M('bonus_tax')->where("statu=1")->cache('taxList')->select());
        $this->assign('moneylist', M('money')->where("statu=1")->cache('moneylist')->select());
        $this->assign('moneyLogType', moneyLogType());
    }

    public function moneyLog()
    {
        $condition = array();
        I('mid') && $condition['mid'] = I('mid');
        I('type') && $condition['is_type'] = I('type');
        $condition['uid'] = $this->user_id;

        $model = M('money_log');
        $list1 = $model->where($condition)->getField('id, is_type');
        $count = count($list1);

        $p = I('p');
        $pSize = 10;
        $list = $model->where($condition)->limit($p * $pSize, $pSize)->order('id desc')->select();
        if (IS_AJAX) {
            $this->assign('list', $list);
            $this->display('moneyLogAjax');
        } else {
            $this->assign('typeList', array_unique($list1));
            $bonusIdArr = M('bonus')->getField('name_cn, bonus_id');
            $incomeWhere = $carryWhere = $expendWhere = array();
            $incomeWhere['uid'] = $carryWhere['uid'] = $expendWhere['uid'] = $this->user_id;
            $incomeWhere['is_type'] = array('in', [1, 2, 3]);
            $carryWhere['statu'] = 1;
            $expendWhere['is_type'] = array('not in', array(108));
            $expendWhere['mid'] = ['in', [2, 3, 4, 5]];
            $expendWhere['money'] = ['lt', 0];
            if ($_GET['mid']) {
                $incomeWhere['mid'] = $carryWhere['mid'] = $expendWhere['mid'] = $_GET['mid'];
            }
            $this->assign('totalIncome', floatval(M('money_log')->where($incomeWhere)->sum('money')));
            $this->assign('totalCarry', floatval(M('money_carry_log')->where($incomeWhere)->sum('add_money')));
            $this->assign('totalExpend', abs(floatval(M('money_log')->where($expendWhere)->sum('money'))));
            $this->assign('count', $count);
            $this->display('moneyLog');
        }
    }
  
  	/**
     * 分享积分
     */
    public function fenmoneyLog()
    {
        $condition = [
            'uid' => $this->user_id,
            'bonus_id' => 3,
            'money' => ['gt', 0],
            'statu' => 9
        ];
        $count = M('bonus_log')->where($condition)->count();

        if (IS_AJAX) {
            $p = I('p') > 0 ? I('p') : 0;
            $pSize = 10;
            $result = M('bonus_log')->where($condition)->order('id desc')->limit(($p * $pSize) . ',' . $pSize)->select();

            $this->assign('list', $result);
            $this->display('fenmoneyLogAjax');
            exit;
        }
        $this->assign('count', $count);
        $this->display('fenmoneyLog');
    }

    public function userDay() {
        if (IS_AJAX) {
            $condition = array();
            if (I('time')) {
                $time = explode(' - ', I('time'));
                $addTime = strtotime($time[0]);
                $outTime = strtotime($time[1]) + 86400;
                $condition['zf_time'] = array('between', array($addTime, $outTime));
            }
            $condition['uid'] = $this->user_id;
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('users_day')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('users_day')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('userDayAjax');
            die;
        }
        $this->display('userDay');
    }

    public function lockMoney() {
        if (IS_AJAX) {
            $condition = array();
            I('mid') ? $condition['mid'] = I('mid') : false;
            I('statu') ? $condition['statu'] = I('statu') : false;
            if (I('time')) {
                $time = explode(' - ', I('time'));
                $addTime = strtotime($time[0]);
                $outTime = strtotime($time[1]) + 86400;
                $condition['lock_time'] = array('between', array($addTime, $outTime));
            }
            $condition['uid'] = $this->user_id;
            $sort_order = I('order_by', 'id') . ' ' . I('sort', 'desc');
            $count = M('users_money_lock')->where($condition)->count();
            $Page = ajaxGetPage($count, 10);
            $result = M('users_money_lock')->where($condition)->order($sort_order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $userIdArr = getArrColumn($result, 'uid');
            $this->assign('page', $Page->show());
            $this->assign('list', $result);
            $this->display('lockMoneyAjax');
            die;
        }
        $this->display('lockMoney');
    }

    public function userMoney() {

        $this->display('userMoney');
    }

}
