<?php

namespace Mobile\Controller;

class BonusController extends CommonController {

    public function _initialize() {
        parent::_initialize();
    }

    public function index() {

        $bonusMoneyList = M('bonus_log')->where(['uid' => $this->user_id, 'bonus_id' => ['neq', 1]])->group('bonus_id')->field('sum(money) as money, sum(out_money) as out_money, bonus_id')->select();

        $invest = M('users_invest')->where(['uid' => $this->user_id])->field('sum(money_total) as money, sum(total_money) as out_money, sum(price_total) as price_total')->find();

        $invest['bonus_id'] = 1;

        $bonusMoneyList[] = $invest;

        $bonusMoneyList = convertArrKey($bonusMoneyList, 'bonus_id');

        $this->assign('bonusMoneyList', $bonusMoneyList);
        $this->assign('invest', $invest);
        
        $this->display('index');
    }

    public function logIndex() {
        $condition = [
            'uid' => $this->user_id
        ];
        $id = I('id', '', 'intval');
        $id && $condition['bonus_id'] = $id;
        I('is_type') ? $condition['statu'] = I('is_type') : false;
        $model = M('bonus_log');
        $startTime = strtotime(I('add_time'));
        $endTime = strtotime(I('end_time'));
        if ($startTime && $endTime) {
            $condition['add_time'] = array('between', array($startTime, $endTime + 86400));
        } elseif ($startTime > 0) {
            $condition['add_time'] = array('gt', $startTime);
        } elseif ($endTime > 0) {
            $condition['add_time'] = array('lt', $endTime);
        }
        $count = $model->where($condition)->select();
        if (IS_AJAX) {
            $p = I('p') > 0 ? I('p') : 0;
            $pSize = 20;
            $sort_order = (I('order') ? I('order') : 'id') . ' ' . (I('sort') ? I('sort') : 'desc');
            $list = $model->where($condition)->order($sort_order)->limit($p * $pSize, $pSize)->select();

            $comeUserIds = getArrcolumn($list, 'come_uid');

            $comeUserIds && $this->assign('comeUsers', M('users')->where(['user_id' => ['in', $comeUserIds]])->getField('user_id, account'));

            $this->assign('list', $list);
            $this->display('logIndexAjax');
        } else {

            $this->assign('bonusLogStatu', bonusLogStatu());
            $this->assign('count', $count);

            $this->display('logIndex');
        }
    }

}
