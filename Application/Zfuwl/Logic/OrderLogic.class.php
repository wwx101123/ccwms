<?php

namespace Zfuwl\Logic;

use Zfuwl\Model\CommonModel;

class OrderLogic extends CommonModel
{

    protected $tableName;

    public function __construct($tableNmae = 'order')
    {
        $this->tableName = $tableNmae;
        parent::__construct();
    }

    /**
     * 确认发货
     * @param string $orderId 订单id
     * @param string $shoppingName 物流名称
     * @retrun bool 操作状态
     */
    public function confirmDeliver($orderId, $shoppingName)
    {

        $orderId = is_array($orderId) ? implode(',', $orderId) : $orderId;
        $orderData = array(
            'shopping_name' => $shoppingName,
            'shopping_time' => time(),
            'statu' => 3
        );

        $where = array(
            'order_id' => array('in', $orderId)
        );

        return $this->where($where)->save($orderData);
    }

    /**
     * 删除订单
     * @param string $orderId 订单id
     * @return bool 删除状态
     */
    public function delOrder($orderId)
    {
        $orderId = is_array($orderId) ? implode(',', $orderId) : $orderId;
        $where = array(
            'order_id' => array('in', $orderId)
        );

        $orderData = array(
            'order_status' => 5
        );

        return $this->where($where)->save($orderData);
    }
    /**
     * 根据订单ID获取商品列表
     * @param $order_id
     * @return mixed
     */
    public function getOrderGoodsByOrderId($order_id)
    {
        return D('Common/OrderGoods')->relation(true)->where(['order_id'=>$order_id])->select();
    }
}