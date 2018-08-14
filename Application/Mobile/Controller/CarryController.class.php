<?php

namespace Mobile\Controller;

use Zfuwl\Logic\CarryLogic;

class CarryController extends CommonController {

    public function _initialize() {
        parent::_initialize();
    }

    public function index() {
        $condition = array(
            'uid' => $this->user_id
        );
        $count = M('money_carry_log')->where($condition)->count();
        if (IS_AJAX) {
            $p = I('p') > 0 ? I('p') : 0;
            $pSize = 10;

            $list = M('money_carry_log')->where($condition)->limit(($p * $pSize) . ',' . $pSize)->order(array('add_time' => 'desc'))->select();

            $openingIdArr = unArrNull(getArrColumn($list, 'opening_id'));
            $openingIdArr && $this->assign('bankList', M('bank')->where(array('opening_id' => array('in', $openingIdArr)))->getField('id, name_cn, img'));

            $this->assign('list', $list);

            $this->display('indexAjax');
        } else {
            $this->assign('count', $count);

            $this->display('index');
        }
    }

    /**
     * 提现支持列表
     */
    public function carryMoneyList() {
        $carryList = M('money_carry')->where(array('statu' => 1))->group('mid')->field('mid,low,bei,out,fee')->select();

        $this->assign('carryList', $carryList);

        $this->assign('levelInfo', levelInfo($this->user['level']));

        $this->display('carryMoneyList');
    }

    /**
     * 提现申请
     */
    public function carryAdd() {
        $mid = I('mid', '', 'intval');
        if ($mid <= 0) {
            $this->error('操作失败');
        }
        if (IS_POST) {
            $post = I('post.');

            $carrLogic = new CarryLogic();
            $res = $carrLogic->carryAdd($post, $this->user_id);
            if ($res['status'] == 1) {
                $this->success($res['msg'], U("Carry/index"));
            } else {
                $this->error($res['msg']);
            }
        } else {
            $info = M('money_carry')->where(array('mid' => $mid, 'statu' => 1))->find();
            if (!$info) {
                $this->error('该钱包暂时不能提现');
            }

            $userInfo = D("UserView")->where(array('user_id' => $this->user_id))->field('opening_id,bank_account,bank_name')->find();
            $bankInfo = M('bank')->where(array('id' => $userInfo['opening_id']))->find();

            $this->assign('bankInfo', $bankInfo);
            $this->assign('userInfo', $userInfo);
            $this->assign('info', $info);
            $this->assign('mid', $mid);
            $this->display('carryAdd');
        }
    }

}
