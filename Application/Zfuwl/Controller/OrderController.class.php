<?php

namespace Zfuwl\Controller;

use Zfuwl\Logic\OrderLogic;

class OrderController extends CommonController {

    public $orderLogic;
    public $orderGoodsLogic;

    public function _initialize() {

        parent::_initialize();
        $this->orderLogic = new OrderLogic();
        $this->orderGoodsLogic = new OrderLogic('order_goods');
        $this->assign('orderStatu', orderStatu());
    }

    /**
     * 订单列表
     */
    public function index() {

        if (IS_AJAX) {
            $condition = array();
            I('account') && $condition['uid'] = D('users')->selectAll("account = '{$_POST['account']}'", array('user_id'), 1);
            I('order_sn') && $condition['order_sn'] = I('order_sn');
            I('statu') && $condition['statu'] = I('statu');
            if (I('time')) {
                $time = explode(' - ', I('time'));
                $addTime = strtotime($time[0]);
                $outTime = strtotime($time[1]) + 86400;
                $condition['add_time'] = array('between', array($addTime, $outTime));
            }
            $orderResult = $this->orderLogic->selectAllListAjax($condition, array('add_time' => 'desc'));

            $userIdArr = getArrColumn($orderResult['list'], 'uid');

            $userIdArr && $this->assign('userList', D('users')->selectAll("user_id in(" . implode(',', $userIdArr) . ")", array('user_id', 'account'), 1));

            $this->assign('orderList', $orderResult['list']);
            $this->assign('page', $orderResult['page']);

            $this->display('indexAjax');
        } else {
            $this->display('index');
        }
    }

    /**
     * 订单详情
     */
    public function detail() {
        $orderId = I('order_id', '', 'intVal');
        $order = $this->orderLogic->findDataByField('order_id', $orderId);
        $user = D('Users')->findDataByField('user_id', $order['uid']);
        $orderGoodsList = $this->orderGoodsLogic->selectAll("order_id = {$orderId}");
        $this->assign('orderGoodsList', $orderGoodsList);
        $this->assign('order', $order);
        $this->assign('user', $user);
        $this->assign('region', M('region')->getField('id, name_cn'));
        $this->display('detail');
    }

    /**
     * 确认发货
     */
    public function confirmDeliver() {
        $orderId = I('id');
        $shoppingName = I('shopping_name');
        $res = $this->orderLogic->confirmDeliver($orderId, $shoppingName);
        if ($res) {
            $this->success('操作成功!');
        } else {
            $this->error('操作失败!');
        }
    }

    /**
     * 删除订单
     */
    public function delOrder() {
        $orderId = I('id');
        $res = $this->orderLogic->delOrder($orderId);
        if ($res) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }

}
