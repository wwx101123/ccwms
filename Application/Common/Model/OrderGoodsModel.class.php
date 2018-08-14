<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/26 0026
 * Time: 18:01
 */

namespace Common\Model;


use Think\Model\RelationModel;

class OrderGoodsModel extends RelationModel
{

    protected $_link = array(
        'goods'=>array(
            'mapping_type'      => self::HAS_ONE,
            'class_name'        => 'goods',
            // 定义更多的关联属性
            'foreign_key'=>'goods_id',
            'mapping_key'=>'goods_id',
//            'mapping_fields '=>['name'],
            'as_fields'=>'picture,rebate'
        ),

    );
    /**
     * 根据订单ID获取商品列表
     * @param $order_id
     * @return mixed
     */
    public function getOrderGoodsByOrderId($order_id)
    {
        return $this->relation(true)->where(['order_id'=>$order_id])->select();
    }
}